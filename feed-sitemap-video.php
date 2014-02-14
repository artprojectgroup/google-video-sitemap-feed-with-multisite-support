<?php
/**
 * XML Sitemap Feed Template for displaying an XML Sitemap feed.
 *
 * @package Google Video Sitemap Feed With Multisite Support plugin for WordPress
 */

//Variables globales
global $tabla, $apis, $error_404;

$tabla = $wpdb->base_prefix . "xml_sitemap_video";
$apis = array('youtube' => 'http://gdata.youtube.com/feeds/api/videos/', 'dailymotion' => 'https://api.dailymotion.com/video/', 'vimeo' => 'http://vimeo.com/api/v2/video/');
$error_404 = false;

$wpdb->query("create table IF NOT EXISTS $tabla (id int UNSIGNED auto_increment PRIMARY KEY, contenido mediumtext NOT NULL, video text NOT NULL, proveedor text NOT NULL) default charset=utf8;"); //Crea la base de datos si es necesario

//Consulta para obtener los datos de la entrada que contiene el vídeo
function xml_sitemap_video_consulta($video) {
	global $wpdb;
	
	$consulta = get_transient('xml_sitemap_video_consulta');
	if ($consulta === false) 
	{
	     $consulta = $wpdb->get_results("SELECT id, post_title FROM $wpdb->posts WHERE post_status = 'publish' AND (post_type = 'post' OR post_type = 'page') AND (post_content LIKE '%$video%')");
	     set_transient('xml_sitemap_video_consulta', $consulta, 30 * DAY_IN_SECONDS);
	}
	if (isset($consulta->query)) $consulta = $consulta->query;
	
	return $consulta;
}

//Envía un correo informando de que el vídeo ya no existe
function xml_sitemap_video_envia_correo($identificador) {
	$entrada = xml_sitemap_video_consulta($identificador);

	wp_mail(get_option('admin_email'), __('Video not found!', 'xml_video_sitemap'), sprintf(__('Please check post <a href="%s">%s</a> on your blog %s and edit the deleted video id %s.<br /><br />email sended by <a href="http://www.artprojectgroup.es/plugins-para-wordpress/google-video-sitemap-feed-with-multisite-support">Google Video Sitemap Feed With Multisite Support</a>', 'xml_video_sitemap'), get_permalink($entrada[0]->id), $entrada[0]->post_title, get_bloginfo('name'), $identificador), "Content-type: text/html");
}

//Procesa correctamente las entidades del RSS
$entity_custom_from = false; 
$entity_custom_to = false;
function xml_sitemap_video_html_entity($data) {
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

//Añadimos un nuevo intervalo mensual
function xml_sitemap_video_mensual($schedules) { 
	$schedules['monthly'] = array(
		'interval' => 2635200,
		'display' => __('Once a month')
	); 
	
    return $schedules;
}
add_filter('cron_schedules', 'xml_sitemap_video_mensual');
 
//Activamos la tarea programada si es necesario
function xml_sitemap_video_activacion_limpieza() {
    if (!wp_next_scheduled('xml_sitemap_video_limpieza')) wp_schedule_event(time(), 'monthly', 'xml_sitemap_video_limpieza');
}
add_action('wp', 'xml_sitemap_video_activacion_limpieza');
 
//Limpiamos la base de datos una vez al mes
function xml_sitemap_video_limpia() {
	global $wpdb, $tabla, $apis, $error_404;

	$videos = get_transient('xml_sitemap_video_consulta');
	if ($videos === false) 
	{
	     $videos = $wpdb->get_results("select video, proveedor from $tabla");
	     set_transient('xml_sitemap_video_limpia', $videos, 30 * DAY_IN_SECONDS);
	}
	if (!empty($videos)) 
	{
		foreach ($videos as $video) 
		{
			$entradas = xml_sitemap_video_consulta($video->video);
			if (empty($entradas)) $wpdb->query("delete from $tabla where video = '$video->video'"); //Borramos un vídeo que ya no está en WordPress
			else
			{
				if ($video->proveedor == 'vimeo') $url = $apis[$video->proveedor] . $identificador . ".json";
				else $url = $apis[$video->proveedor] . $identificador;
				
				$contenido = xml_sitemap_video_curl($url);
				$dailymotion = NULL;
				if ($video->proveedor == 'dailymotion') $dailymotion = json_decode($contenido);
				
				if (!$error_404) $wpdb->query("update $tabla set contenido = '" . mysql_real_escape_string($contenido) . "' where video = '$video->video'"); //Actualiza el contenido
				else if ($contenido == 'Video not found' || $contenido == 'Invalid id' || $contenido != 'Private video' || isset($dailymotion->error)) $wpdb->query("delete from $tabla where video = '$video->video'");
			}
		}
	}
}
add_action('xml_sitemap_video_limpieza', 'xml_sitemap_video_limpia');

//Obtiene información del vídeo (función mejorada con ayuda de Ludo Bonnet [https://github.com/ludobonnet])
function xml_sitemap_video_procesa_url($url, $video, $proveedor) {
	global $wpdb, $tabla, $error_404;

	$informacion = get_transient('xml_sitemap_video_procesa_url');
	if ($informacion === false) 
	{
	     $informacion = $wpdb->get_results("select contenido from $tabla where video = '$video'");
	     set_transient('xml_sitemap_video_procesa_url', $informacion, 30 * DAY_IN_SECONDS);
	}
	if (empty($informacion))
	{
		$contenido = xml_sitemap_video_curl($url);
		$dailymotion = NULL;
		if ($proveedor == 'dailymotion') $dailymotion = json_decode($contenido);
		
		if ($contenido != 'Video not found' && $contenido != 'Invalid id' && $contenido != 'Private video' && !isset($dailymotion->error) && !$error_404) 
		{
			$wpdb->query("insert into $tabla (contenido, video, proveedor) values ('" . mysql_real_escape_string($contenido) . "', '$video', '$proveedor')"); //Almacena el contenido en la base de datos
			return $contenido; 
		}
		else 
		{
			$error_404 = false;
			xml_sitemap_video_envia_correo($video);
			return false; 
		}
	}
	else return $informacion[0]->contenido;
}

//Lee las URL externas	
function xml_sitemap_video_curl($url) { 
	global $error_404;
	
	$error_404 = false;
	
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_TIMEOUT, 10);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$contenido = curl_exec($ch);
	$cabecera = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	if ($cabecera == 404) $error_404 = true;
	curl_close($ch);
		
	return $contenido; 
}

//Procesa los datos externos
function xml_sitemap_video_informacion($identificador, $proveedor) {
	global $apis;

	switch ($proveedor) 
	{
		case 'youtube':
			return simplexml_load_string(xml_sitemap_video_procesa_url($apis[$proveedor] . $identificador, $identificador, $proveedor));
			break;
		case 'dailymotion':
			return json_decode(xml_sitemap_video_procesa_url($apis[$proveedor] . $identificador, $identificador, $proveedor));
			break;
		case 'vimeo':
			$vimeo = json_decode(xml_sitemap_video_procesa_url($apis[$proveedor] . $identificador . ".json", $identificador, $proveedor));
			return $vimeo[0];
			break;
    }
	
	return false;
}

status_header('200'); // force header('HTTP/1.1 200 OK') for sites without posts
header('Content-Type: text/xml; charset=' . get_bloginfo('charset'), true);

echo '<?xml version="1.0" encoding="' . get_bloginfo('charset') . '"?>
<!-- Created by Google Video Sitemap Feed With Multisite Support by Art Project Group (http://www.artprojectgroup.es/plugins-para-wordpress/google-video-sitemap-feed-with-multisite-support) -->
<!-- Generated-on="' . date('Y-m-d\TH:i:s+00:00') . '" -->
<?xml-stylesheet type="text/xsl" href="' . get_bloginfo('wpurl') . '/wp-content/plugins/google-video-sitemap-feed-with-multisite-support/video-sitemap.xsl"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:video="http://www.google.com/schemas/sitemap-video/1.1">' . PHP_EOL;

$entradas = get_transient('xml_sitemap_video');
if ($entradas === false) 
{
     $entradas = $wpdb->get_results("(SELECT id, post_title, post_content, post_date, post_excerpt, post_author
                                    FROM $wpdb->posts
                                    WHERE post_status = 'publish'
                                        AND (post_type = 'post' OR post_type = 'page')
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
                                            AND (post_type = 'post' OR post_type = 'page'))
                                ORDER BY post_date DESC"); //Consulta mejorada con ayuda de Ludo Bonnet [https://github.com/ludobonnet]
     set_transient('xml_sitemap_video', $entradas, 30 * DAY_IN_SECONDS);
}

global $wp_query;
$wp_query->is_404 = false;	//force is_404() condition to false when on site without posts
$wp_query->is_feed = true;	//force is_feed() condition to true so WP Super Cache includes the sitemap in its feeds cache

if (!empty($entradas)) 
{
	$videos = $video_procesado = array();
	
	if (isset($entradas->query)) $entradas = $entradas->query;
	foreach ($entradas as $entrada) 
	{
		$entrada->ID = $entrada->id; //Necesario para evitar notificaciones de error
		setup_postdata($entrada);
		$contenido = $entrada->post_content;

		if (preg_match_all('/youtube\.com\/(v\/|watch\?v=|embed\/)([^\$][a-zA-Z0-9\-_]*)/', $contenido, $busquedas, PREG_SET_ORDER) || preg_match_all('/youtube-nocookie\.com\/(v\/|watch\?v=|embed\/)([^\$][a-zA-Z0-9\-_]*)/', $contenido, $busquedas, PREG_SET_ORDER)) //Youtube
		{
			foreach ($busquedas as $busqueda) $videos[] = array('proveedor' => 'youtube', 'identificador' => $busqueda[2], 'reproductor' => "http://youtube.googleapis.com/v/$busqueda[2]", 'imagen' => "http://i.ytimg.com/vi/$busqueda[2]/hqdefault.jpg");
		}
		if (preg_match_all('/youtu\.be\/([^\$][a-zA-Z0-9\-_]*)/', $contenido, $busquedas, PREG_SET_ORDER)) //Acortador de Youtube
		{
			foreach ($busquedas as $busqueda) $videos[] = array('proveedor' => 'youtube', 'identificador' => $busqueda[1], 'reproductor' => "http://youtube.googleapis.com/v/$busqueda[1]", 'imagen' => "http://i.ytimg.com/vi/$busqueda[1]/hqdefault.jpg");
		}
		if (preg_match_all('/dailymotion\.com\/(video\/)([^\$][a-zA-Z0-9]*)/', $contenido, $busquedas, PREG_SET_ORDER)) //Dailymotion. Añadido por Ludo Bonnet [https://github.com/ludobonnet]
		{
			foreach ($busquedas as $busqueda) if (is_numeric($busqueda[2])) $videos[] = array('proveedor' => 'dailymotion', 'identificador' => $busqueda[2], 'reproductor' => "http://www.dailymotion.com/embed/video/$busqueda[2]", 'imagen' => "http://www.dailymotion.com/thumbnail/video/$busqueda[2]");
		}
		if (preg_match_all('/vimeo\.com\/moogaloop.swf\?clip_id=([^\$][0-9]*)/', $contenido, $busquedas, PREG_SET_ORDER) || preg_match_all('/vimeo\.com\/video\/([^\$][0-9]*)/', $contenido, $busquedas, PREG_SET_ORDER) || preg_match_all('/vimeo\.com\/([^\$][0-9]*)/', $contenido, $busquedas, PREG_SET_ORDER)) //Vimeo. Mejorado a partir del código aportado por Ludo Bonnet [https://github.com/ludobonnet]
		{
			foreach ($busquedas as $busqueda) if (is_numeric($busqueda[1])) $videos[] = array('proveedor' => 'vimeo', 'identificador' => $busqueda[1], 'reproductor' => "http://player.vimeo.com/video/$busqueda[1]");
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
				$informacion = xml_sitemap_video_informacion($video['identificador'], $video['proveedor']);
				if (!$informacion) continue;
				
				if ($contador > 0) $multiple = true;
				if ($multiple) 
				{
					$titulo .= " | " . $informacion->title;
					$descripcion = $extracto . " " .$informacion->title;
				}
				else $descripcion = $extracto;

				if ($video['proveedor'] == 'vimeo') $video['imagen'] = $informacion->thumbnail_large;
				$contador++;
				
				echo "\t" . '<url>' . PHP_EOL;
				echo "\t\t" . '<loc>' . $enlace . '</loc>' . PHP_EOL;
				echo "\t\t" . '<video:video>' . PHP_EOL;
				echo "\t\t" . '<video:player_loc allow_embed="yes" autoplay="autoplay=1">' . $video['reproductor'] . '</video:player_loc>' . PHP_EOL;
				echo "\t\t" . '<video:thumbnail_loc>'. $video['imagen'] .'</video:thumbnail_loc>' . PHP_EOL;
				echo "\t\t" . '<video:title>' . xml_sitemap_video_html_entity(html_entity_decode($titulo, ENT_QUOTES, 'UTF-8')) . '</video:title>' . PHP_EOL;
				echo "\t\t" . '<video:description>' . xml_sitemap_video_html_entity(html_entity_decode($descripcion, ENT_QUOTES, 'UTF-8')) . '</video:description>' . PHP_EOL;
    
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
