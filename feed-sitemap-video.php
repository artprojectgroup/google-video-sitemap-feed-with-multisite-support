<?php
/**
 * XML Sitemap Feed Template for displaying an XML Sitemap feed.
 *
 * @package Google Video Sitemap Feed With Multisite Support plugin for WordPress
 */

set_time_limit(300); //Limitación de tiempo para CURL añadido por Ludo Bonnet [https://github.com/ludobonnet])

//Procesa correctamente las entidades del RSS
$entity_custom_from = false; 
$entity_custom_to = false;

function sitemap_video_html_entity($data) {
	global $entity_custom_from, $entity_custom_to;
	
	if(!is_array($entity_custom_from) || !is_array($entity_custom_to)) {
		$array_position = 0;
		foreach (get_html_translation_table(HTML_ENTITIES) as $key => $value) {
			switch ($value) {
				case '&nbsp;':
					break;
				case '&gt;':
				case '&lt;':
				case '&quot;':
				case '&apos;':
				case '&amp;':
					$entity_custom_from[$array_position] = $key; 
					$entity_custom_to[$array_position] = $value; 
					$array_position++; 
					break; 
				default: 
					$entity_custom_from[$array_position] = $value; 
					$entity_custom_to[$array_position] = $key; 
					$array_position++; 
			} 
		}
	}
	return str_replace($entity_custom_from, $entity_custom_to, $data); 
}

//Obtiene información del vídeo (función mejorada con ayuda de Ludo Bonnet [https://github.com/ludobonnet])
function sitemap_video_youtube($identificador) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "http://gdata.youtube.com/feeds/api/videos/$identificador");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $youtube = curl_exec($ch);
    curl_close($ch);
    if ($youtube == 'Video not found' OR $youtube == 'Invalid id') return false;
	
	return simplexml_load_string($youtube);
}

function sitemap_video_dailymotion($identificador) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.dailymotion.com/video/$identificador");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $dailymotion = json_decode(curl_exec($ch));
    curl_close($ch);
	
    return $dailymotion;
}

function sitemap_video_vimeo($identificador) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://vimeo.com/api/v2/video/$identificador.json");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $vimeo = json_decode(curl_exec($ch));
    curl_close($ch);
	
    return $vimeo[0];
}

function sitemap_video_informacion($identificador, $proveedor) {
    switch ($proveedor) {
        case 'youtube':
            return sitemap_video_youtube($identificador);
            break;
        case 'dailymotion':
            return sitemap_video_dailymotion($identificador);
            break;
        case 'vimeo':
            return sitemap_video_vimeo($identificador);
            break;
    }
    return '';
}

status_header('200'); // force header('HTTP/1.1 200 OK') for sites without posts
header('Content-Type: text/xml; charset=' . get_bloginfo('charset'), true);

echo '<?xml version="1.0" encoding="' . get_bloginfo('charset') . '"?>
<!-- Created by Google Video Sitemap Feed With Multisite Support by Art Project Group (http://www.artprojectgroup.es/plugins-para-wordpress/google-video-sitemap-feed-with-multisite-support) -->
<!-- Generated-on="' . date('Y-m-d\TH:i:s+00:00') . '" -->
<?xml-stylesheet type="text/xsl" href="' . get_bloginfo('wpurl') . '/wp-content/plugins/google-video-sitemap-feed-with-multisite-support/video-sitemap.xsl"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:video="http://www.google.com/schemas/sitemap-video/1.1">' . PHP_EOL;

$entradas = $wpdb->get_results ("(SELECT id, post_title, post_content, post_date, post_excerpt
                                    FROM $wpdb->posts
                                    WHERE post_status = 'publish'
                                        AND (post_type = 'post' OR post_type = 'page')
                                        AND (post_content LIKE '%youtube.com%'
                                            OR post_content LIKE '%youtube-nocookie.com%'
                                            OR post_content LIKE '%youtu.be%'                              
                                            OR post_content LIKE '%dailymotion.com%'
                                            OR post_content LIKE '%vimeo.com%'))
                                UNION ALL
                                    (SELECT id, post_title, meta_value as 'post_content', post_date, post_excerpt
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
                                            AND (post_type = 'post' OR post_type = 'page'))
                                ORDER BY post_date DESC"); //Consulta mejorada con ayuda de Ludo Bonnet [https://github.com/ludobonnet]
	
global $wp_query;
$wp_query->is_404 = false;	// force is_404() condition to false when on site without posts
$wp_query->is_feed = true;	// force is_feed() condition to true so WP Super Cache includes the sitemap in its feeds cache

if (!empty($entradas)) 
{
	$videos = $video_procesado = array();

	foreach ($entradas as $entrada) 
	{
		setup_postdata($entrada);
		$contenido = $entrada->post_content;

		if (preg_match_all ('/youtube\.com\/(v\/|watch\?v=|embed\/)([^\$][a-zA-Z0-9\-_]*)/', $contenido, $busquedas, PREG_SET_ORDER) || preg_match_all ('/youtube-nocookie\.com\/(v\/|watch\?v=|embed\/)([^\$][a-zA-Z0-9\-_]*)/', $contenido, $busquedas, PREG_SET_ORDER)) //Youtube
		{
			foreach ($busquedas as $busqueda) $videos[] = array('proveedor' => 'youtube', 'identificador' => $busqueda[2], 'reproductor' => "http://youtube.googleapis.com/v/$busqueda[2]", 'imagen' => "http://i.ytimg.com/vi/$busqueda[2]/hqdefault.jpg");
		}
		if (preg_match_all ('/youtu\.be\/([^\$][a-zA-Z0-9\-_]*)/', $contenido, $busquedas, PREG_SET_ORDER)) //Acortador de Youtube
		{
			foreach ($busquedas as $busqueda) $videos[] = array('proveedor' => 'youtube', 'identificador' => $busqueda[1], 'reproductor' => "http://youtube.googleapis.com/v/$busqueda[1]", 'imagen' => "http://i.ytimg.com/vi/$busqueda[1]/hqdefault.jpg");
		}
		if (preg_match_all ('/dailymotion.com\/(video\/)([^\$][a-zA-Z0-9]*)/', $contenido, $busquedas, PREG_SET_ORDER)) //Dailymotion. Añadido por Ludo Bonnet [https://github.com/ludobonnet]
		{
			foreach ($busquedas as $busqueda) $videos[] = array('proveedor' => 'dailymotion', 'identificador' => $busqueda[2], 'reproductor' => "http://www.dailymotion.com/embed/video/$busqueda[2]", 'imagen' => "http://www.dailymotion.com/thumbnail/video/$busqueda[2]");
		}
		if (preg_match_all ('/vimeo.com\/([^\$][0-9]*)/', $contenido, $busquedas, PREG_SET_ORDER))  //Vimeo. Añadido por Ludo Bonnet [https://github.com/ludobonnet]
		{
			foreach ($busquedas as $busqueda) $videos[] = array('proveedor' => 'vimeo', 'identificador' => $busqueda[1], 'reproductor' => "http://player.vimeo.com/video/$busqueda[1]");
		}

		if (!empty($videos)) //Mejorado con ayuda de Ludo Bonnet [https://github.com/ludobonnet]
		{
			$extracto = ($entrada->post_excerpt != "") ? $entrada->post_excerpt : get_the_excerpt(); 
			$enlace = htmlspecialchars(get_permalink($entrada->id));
			$contador = 0;
			$multiple = false;
	
			foreach ($videos as $video) 
			{
				if (in_array($video['identificador'], $video_procesado)) continue;
				
				array_push($video_procesado, $video['identificador']);

				$titulo = $entrada->post_title;

				if ($contador > 0) $multiple = true;
				if ($multiple) 
				{
					$informacion = sitemap_video_informacion($video['identificador'], $video['proveedor']);
					if (!$informacion) continue;
					$descripcion = $titulo . ". " . $extracto;
					$titulo .= ". " . $informacion->title;
					if ($video['proveedor'] == 'vimeo') $video['imagen'] = $informacion->thumbnail_large;
				}
				else 
				{
					$descripcion = $extracto;
					if ($video['proveedor'] == 'vimeo') 
					{
						$informacion = sitemap_video_informacion($video['identificador'], $video['proveedor']);
						$video['imagen'] = $informacion->thumbnail_large;
					}
				}
				$contador++;
				
				echo "\t" . '<url>' . PHP_EOL;
				echo "\t\t" . '<loc>' . $enlace . '</loc>' . PHP_EOL;
				echo "\t\t" . '<video:video>' . PHP_EOL;
				echo "\t\t" . '<video:player_loc allow_embed="yes" autoplay="autoplay=1">' . $video['reproductor'] . '</video:player_loc>' . PHP_EOL;
				echo "\t\t" . '<video:thumbnail_loc>'. $video['imagen'] .'</video:thumbnail_loc>' . PHP_EOL;
				echo "\t\t" . '<video:title>' . sitemap_video_html_entity(html_entity_decode($titulo, ENT_QUOTES, 'UTF-8')) . '</video:title>' . PHP_EOL;
				echo "\t\t" . '<video:description>' . sitemap_video_html_entity(html_entity_decode($descripcion, ENT_QUOTES, 'UTF-8')) . '</video:description>' . PHP_EOL;
    
				$etiquetas = get_the_tags($entrada->id); 
				if ($etiquetas) 
				{ 
                	$numero_de_etiquetas = 0;
                	foreach ($etiquetas as $etiqueta) 
					{
                		if ($numero_de_etiquetas++ > 32) break;
                		echo "\t\t" . '<video:tag>' . $etiqueta->name . '</video:tag>' . PHP_EOL;
                	}
				}    

				$categorias = get_the_category($entrada->id); 
				if ($categorias) 
				{ 
                	foreach ($categorias as $categoria) 
					{
                		echo "\t\t" . '<video:category>' . $categoria->name . '</video:category>' . PHP_EOL;
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
