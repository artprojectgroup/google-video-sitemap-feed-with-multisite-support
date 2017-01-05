<?php
/**
 * XML Sitemap Feed Template for displaying an XML Sitemap feed.
 *
 * @package Google Video Sitemap Feed With Multisite Support plugin for WordPress
 */

//Envía un correo informando de que el vídeo ya no existe
function xml_sitemap_video_envia_correo( $video ) {
	global $wpdb, $busqueda;

	$entrada = $wpdb->get_results( "SELECT id, post_title FROM $wpdb->posts WHERE post_status = 'publish' $busqueda AND (post_content LIKE '%$video%')" );

	wp_mail( get_option( 'admin_email' ), __( 'Video not found!', 'xml_video_sitemap' ), sprintf( __( 'Please check post <a href="%s">%s</a> on your blog %s and edit the deleted video id %s.<br /><br />email sended by <a href="http://www.artprojectgroup.es/plugins-para-wordpress/google-video-sitemap-feed-with-multisite-support">Google Video Sitemap Feed With Multisite Support</a>', 'xml_video_sitemap' ), get_permalink( $entrada[0]->id ), $entrada[0]->post_title, get_bloginfo( 'name' ), $video ), "Content-type: text/html" );
}

//Obtiene información del vídeo ( función mejorada con ayuda de Ludo Bonnet [https://github.com/ludobonnet] )
function xml_sitemap_video_procesa_url( $url, $video ) {
	$respuesta = get_transient( $url );
	if ( false === $respuesta ) {
		$respuesta = wp_remote_get( $url );
		set_transient( $url, $respuesta, 30 * DAY_IN_SECONDS );
		$configuracion[$url] = $url;
		if ( get_option( 'xml_sitemap_video' ) || get_option( 'xml_sitemap_video' ) == NULL ) {
			update_option( 'xml_sitemap_video', $configuracion[$url] );
		} else {
			add_option( 'xml_sitemap_video', $configuracion[$url] );
		}
	}
	$configuracion = get_option( 'xml_video_sitemap' );
	if ( !is_wp_error( $respuesta ) ) {
		$dailymotion = json_decode( $respuesta['body'] );
		if ( ( $respuesta['response']['code'] == 404 || $respuesta['body'] == 'Video not found' || $respuesta['body'] == 'Invalid id' || $respuesta['body'] == 'Private video' || isset( $dailymotion->error ) ) && $configuracion['correo'] == "1" ) {
			xml_sitemap_video_envia_correo( $video );
			return NULL;
		}
	} else if ( $configuracion['correo'] == "1" ) {
		xml_sitemap_video_envia_correo( $video );
		return NULL;
	}

	return $respuesta['body']; 
}

//Procesa los datos externos
function xml_sitemap_video_informacion( $identificador, $proveedor ) {
	$apis = array( 
		'youtube'		=> 'https://noembed.com/embed?url=https://www.youtube.com/watch?v=', 
		'dailymotion'	=> 'https://api.dailymotion.com/video/', 
		'vimeo'			=> 'http://vimeo.com/api/v2/video/' 
	);
	switch ( $proveedor ) {
		case 'youtube':
			return json_decode( xml_sitemap_video_procesa_url( $apis[$proveedor] . $identificador, $identificador ) );
			break;
		case 'dailymotion':
			return json_decode( xml_sitemap_video_procesa_url( $apis[$proveedor] . $identificador, $identificador ) );
			break;
		case 'vimeo':
			$vimeo = json_decode( xml_sitemap_video_procesa_url( $apis[$proveedor] . $identificador . ".json", $identificador ) );
			return $vimeo[0];
			break;
    }
	
	return false;
}

status_header( '200' ); // force header( 'HTTP/1.1 200 OK' ) for sites without posts
header( 'Content-Type: text/xml; charset=' . get_bloginfo( 'charset' ), true );

echo '<?xml version="1.0" encoding="' . get_bloginfo( 'charset' ) . '"?>
<!-- Created by Google Video Sitemap Feed With Multisite Support by Art Project Group ( http://www.artprojectgroup.es/plugins-para-wordpress/google-video-sitemap-feed-with-multisite-support ) -->
<!-- Generated-on="' . date( 'Y-m-d\TH:i:s+00:00' ) . '" -->
<?xml-stylesheet type="text/xsl" href="' . get_bloginfo( 'wpurl' ) . '/wp-content/plugins/google-video-sitemap-feed-with-multisite-support/assets/video-sitemap.xsl"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:video="http://www.google.com/schemas/sitemap-video/1.1">' . PHP_EOL;

//Añadimos todos los tipos de entradas
$tipos_de_entradas = get_post_types( '', 'names' );
$busqueda = '';
foreach ( $tipos_de_entradas as $tipo_de_entrada ) {
	$busqueda .= "post_type = '$tipo_de_entrada' OR ";
}
$busqueda = substr_replace( $busqueda, '', -4, -1 );
if ( strlen( $busqueda ) ) {
	$busqueda = "AND ($busqueda)";
}

//Generamos la consulta
delete_transient( 'xml_sitemap_video' );
$entradas = get_transient( 'xml_sitemap_video' );
if ( $entradas === false ) {
     $entradas = $wpdb->get_results( "(SELECT id, post_title, post_content, post_date, post_excerpt, post_author
                                    FROM $wpdb->posts
                                    WHERE post_status = 'publish'
                                        $busqueda
                                        AND (post_content LIKE '%youtube.com%'
                                            OR post_content LIKE '%youtube-nocookie.com%'
                                            OR post_content LIKE '%youtu.be%'                              
                                            OR post_content LIKE '%dailymotion.com%'
                                            OR post_content LIKE '%vimeo.com%'))
                                UNION ALL
                                    (SELECT id, post_title, meta_value as 'post_content', post_date, post_excerpt, post_author
                                        FROM $wpdb->posts
                                        JOIN $wpdb->postmeta
                                            ON id = post_id
                                                AND meta_key = 'wpex_post_oembed'
                                                AND (meta_value LIKE '%youtube.com%'
                                                    OR meta_value LIKE '%youtube-nocookie.com%'
                                                    OR meta_value LIKE '%youtu.be%'
                                                    OR meta_value LIKE '%dailymotion.com%'
                                                    OR meta_value LIKE '%vimeo.com%')
                                        WHERE post_status = 'publish'
                                            $busqueda)
                                UNION ALL
                                    (SELECT id, post_title, post_date, post_excerpt, post_author, post_parent
                                        FROM $wpdb->posts
                                        WHERE post_type = 'attachment'
                                                AND post_mime_type like 'video%'
                                                AND post_parent > 0)
                                ORDER BY post_date DESC" ); //Consulta mejorada con ayuda de Ludo Bonnet [https://github.com/ludobonnet]
     set_transient( 'xml_sitemap_video', $entradas, 30 * DAY_IN_SECONDS );
}

global $wp_query;
$wp_query->is_404	= false;	//force is_404( ) condition to false when on site without posts
$wp_query->is_feed	= true;	//force is_feed( ) condition to true so WP Super Cache includes the sitemap in its feeds cache

if ( !empty( $entradas ) ) {
	$videos = $video_procesado = array( );
	
	if ( isset( $entradas->query ) ) {
		$entradas = $entradas->query;
	}
	foreach ( $entradas as $entrada ) {
		$entrada->ID = $entrada->id; //Necesario para evitar notificaciones de error
		setup_postdata( $entrada );
		$contenido = $entrada->post_content;

		if ( preg_match_all( '/youtube\.com\/(v\/|watch\?v=|embed\/)([^\$][a-zA-Z0-9\-_]*)/', $contenido, $busquedas, PREG_SET_ORDER ) || preg_match_all( '/youtube-nocookie\.com\/(v\/|watch\?v=|embed\/)([^\$][a-zA-Z0-9\-_]*)/', $contenido, $busquedas, PREG_SET_ORDER ) ) { //Youtube
			foreach ( $busquedas as $busqueda ) {
				$videos[] = array( 
					'proveedor'		=> 'youtube', 
					'identificador'	=> $busqueda[2], 
					'reproductor'	=> "https://www.youtube.com/embed/$busqueda[2]", 
					'imagen'		=> "http://i.ytimg.com/vi/$busqueda[2]/hqdefault.jpg" 
				);
			}
		}
		if ( preg_match_all( '/youtu\.be\/([^\$][a-zA-Z0-9\-_]*)/', $contenido, $busquedas, PREG_SET_ORDER ) ) { //Acortador de Youtube
			foreach ( $busquedas as $busqueda ) {
				$videos[] = array( 
					'proveedor'		=> 'youtube', 
					'identificador'	=> $busqueda[1], 
					'reproductor'	=> "https://www.youtube.com/embed/$busqueda[1]", 
					'imagen'		=> "http://i.ytimg.com/vi/$busqueda[1]/hqdefault.jpg" 
				);
			}
		}
		if ( preg_match_all( '/dailymotion\.com\/video\/([^\$][a-zA-Z0-9]*)/', $contenido, $busquedas, PREG_SET_ORDER ) ) { //Dailymotion. Añadido por Ludo Bonnet [https://github.com/ludobonnet]	
			foreach ( $busquedas as $busqueda ) {
				$videos[] = array( 
					'proveedor'		=> 'dailymotion', 
					'identificador'	=> $busqueda[1], 
					'reproductor'	=> "http://www.dailymotion.com/embed/video/$busqueda[1]", 
					'imagen'	=> "http://www.dailymotion.com/thumbnail/video/$busqueda[1]" 
				);
			}
		}
		if ( preg_match_all( '/vimeo\.com\/moogaloop.swf\?clip_id=([^\$][0-9]*)/', $contenido, $busquedas, PREG_SET_ORDER ) || preg_match_all( '/vimeo\.com\/video\/([^\$][0-9]*)/', $contenido, $busquedas, PREG_SET_ORDER ) || preg_match_all( '/vimeo\.com\/([^\$][0-9]*)/', $contenido, $busquedas, PREG_SET_ORDER ) ) { //Vimeo. Mejorado a partir del código aportado por Ludo Bonnet [https://github.com/ludobonnet]
			foreach ( $busquedas as $busqueda ) {
				if ( is_numeric( $busqueda[1] ) ) {
					$videos[] = array( 
						'proveedor'		=> 'vimeo', 
						'identificador'	=> $busqueda[1], 
						'reproductor'	=> "https://player.vimeo.com/video/$busqueda[1]" 
					);
				}
			}
		}

		if ( !empty( $videos ) ) { //Mejorado con ayuda de Ludo Bonnet [https://github.com/ludobonnet]
			$extracto = ( $entrada->post_excerpt != "" ) ? $entrada->post_excerpt : get_the_excerpt( ); 
			$enlace = htmlspecialchars( get_permalink( $entrada->id ) );
			$contador = 0;
			$multiple = false;
	
			foreach ( $videos as $video ) {
				if ( in_array( $video['identificador'], $video_procesado ) ) {
					continue;
				}
				
				array_push( $video_procesado, $video['identificador'] );

				$titulo = $entrada->post_title;
				$informacion = xml_sitemap_video_informacion( $video['identificador'], $video['proveedor'] );
				if ( !$informacion ) {
					continue;
				}
				
				if ( $contador > 0 ) {
					$multiple = true;
				}
				if ( $multiple ) {
					$titulo .= " | " . $informacion->title;
					$descripcion = $extracto . " " .$informacion->title;
				} else {
					$descripcion = $extracto;
				}

				if ( $video['proveedor'] == 'vimeo' ) {
					$video['imagen'] = $informacion->thumbnail_large;
				}
				$contador++;
				
				echo "\t" . '<url>' . PHP_EOL;
				echo "\t\t" . '<loc>' . $enlace . '</loc>' . PHP_EOL;
				echo "\t\t" . '<video:video>' . PHP_EOL;
				echo "\t\t" . '<video:player_loc allow_embed="yes" autoplay="ap=1">' . $video['reproductor'] . '</video:player_loc>' . PHP_EOL;
				echo "\t\t" . '<video:thumbnail_loc>'. $video['imagen'] .'</video:thumbnail_loc>' . PHP_EOL;
				echo "\t\t" . '<video:title>' . htmlspecialchars( $titulo, ENT_QUOTES ) . '</video:title>' . PHP_EOL;
				echo "\t\t" . '<video:description>' . htmlspecialchars( $descripcion, ENT_QUOTES ) . '</video:description>' . PHP_EOL;
   
				$etiquetas = get_the_tags( $entrada->id ); 
				if ( $etiquetas ) { 
                	$numero_de_etiquetas = 0;
                	foreach ( $etiquetas as $etiqueta ) {
                		if ( $numero_de_etiquetas++ > 32 ) {
							break;
						}
                		echo "\t\t" . '<video:tag>' . htmlspecialchars( $etiqueta->name, ENT_QUOTES ) . '</video:tag>' . PHP_EOL;
                	}
				}    

				$categorias = get_the_category( $entrada->id ); 
				if ( $categorias )  { 
                	foreach ( $categorias as $categoria ) {
                		echo "\t\t" . '<video:category>' . htmlspecialchars( $categoria->name, ENT_QUOTES ) . '</video:category>' . PHP_EOL;
                		break;
                	}
				}        
				echo "\t\t" . '</video:video>' . PHP_EOL;
				echo "\t" . '</url>' . PHP_EOL;
			}
		}
	}
}
echo "</urlset>";
?>
