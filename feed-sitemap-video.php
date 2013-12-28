<?php
/**
 * XML Sitemap Feed Template for displaying an XML Sitemap feed.
 *
 * @package Google Video Sitemap Feed With Multisite Support plugin for WordPress
 */

//Obtiene información del vídeo
function informacion_del_video($identificador) {
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "http://gdata.youtube.com/feeds/api/videos/$identificador");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	$youtube = simplexml_load_string(curl_exec($ch));
	curl_close($ch);
	
	return $youtube;
}

status_header('200'); // force header('HTTP/1.1 200 OK') for sites without posts
header('Content-Type: text/xml; charset=' . get_bloginfo('charset'), true);

echo '<?xml version="1.0" encoding="' . get_bloginfo('charset') . '"?>
<!-- Created by Google Video Sitemap Feed With Multisite Support by Art Project Group (http://www.artprojectgroup.es/plugins-para-wordpress/google-video-sitemap-feed-with-multisite-support) -->
<!-- Generated-on="' . date('Y-m-d\TH:i:s+00:00') . '" -->
<?xml-stylesheet type="text/xsl" href="' . get_bloginfo('wpurl') . '/wp-content/plugins/google-video-sitemap-feed-with-multisite-support/video-sitemap.xsl"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:video="http://www.google.com/schemas/sitemap-video/1.1">'."\n";

$entradas = $wpdb->get_results ("SELECT id, post_title, post_content, post_date_gmt, post_excerpt FROM $wpdb->posts WHERE post_status = 'publish' AND (post_type = 'post' OR post_type = 'page') AND (post_content LIKE '%youtube.com%' OR post_content LIKE '%youtube-nocookie.com%') ORDER BY post_date DESC"); //Consulta
	
global $wp_query;
$wp_query->is_404 = false;	// force is_404() condition to false when on site without posts
$wp_query->is_feed = true;	// force is_feed() condition to true so WP Super Cache includes the sitemap in its feeds cache

if (!empty($entradas)) 
{
	$videos = array();
	foreach ($entradas as $entrada) 
	{
		setup_postdata($entrada);
		if (preg_match_all ('/youtube.com\/(v\/|watch\?v=|embed\/)([^\$][a-zA-Z0-9\-_]*)/', $entrada->post_content, $videos, PREG_SET_ORDER) || preg_match_all ('/youtube-nocookie.com\/(v\/|watch\?v=|embed\/)([^\$][a-zA-Z0-9\-_]*)/', $entrada->post_content, $videos, PREG_SET_ORDER)) 
		{
			$extracto = ($entrada->post_excerpt != "") ? $entrada->post_excerpt : get_the_excerpt(); 
			$enlace = htmlspecialchars(get_permalink($entrada->id));
			$contador = 0;
	
			foreach ($videos as $video) 
			{
				$identificador = $video[2];
                        
				if (in_array($identificador, $videos)) continue;
                            
				array_push($videos, $identificador);

				if ($contador > 0) $multiple = true;
				else $multiple = false;
				if ($multiple) 
				{
					$youtube = informacion_del_video($identificador);
					$titulo = $youtube->title;
					$descripcion = $titulo . ". " . $extracto;
				}
				else 
				{
					$titulo = $entrada->post_title;
					$descripcion = $extracto;
				}
				$contador++;
				
				echo "\t" . '<url>' . "\n";
				echo "\t\t" . '<loc>' . $enlace . '</loc>' . "\n";
				echo "\t\t" . '<video:video>' . "\n";
				echo "\t\t" . '<video:player_loc allow_embed="yes" autoplay="autoplay=1">http://www.youtube.com/v/' . $identificador . '</video:player_loc>' . "\n";
				echo "\t\t" . '<video:thumbnail_loc>http://i.ytimg.com/vi/' . $identificador . '/hqdefault.jpg</video:thumbnail_loc>' . "\n";
				echo "\t\t" . '<video:title>' . html_entity_decode($titulo, ENT_QUOTES, 'UTF-8') . '</video:title>' . "\n";
				echo "\t\t" . '<video:description>' . html_entity_decode($descripcion, ENT_QUOTES, 'UTF-8') . '</video:description>' . "\n";
    
				$etiquetas = get_the_tags($entrada->id); 
				if ($etiquetas) 
				{ 
                	$numero_de_etiquetas = 0;
                	foreach ($etiquetas as $etiqueta) 
					{
                		if ($numero_de_etiquetas++ > 32) break;
                		echo "\t\t" . '<video:tag>' . $etiqueta->name . '</video:tag>' . "\n";
                	}
				}    

				$categorias = get_the_category($entrada->id); 
				if ($categorias) 
				{ 
                	foreach ($categorias as $categoria) 
					{
                		echo "\t\t" . '<video:category>' . $categoria->name . '</video:category>' . "\n";
                		break;
                	}
				}        
				echo "\t\t" . '</video:video>' . "\n";
				echo "\t" . '</url>' . "\n";
			}
		}
	}
}
echo "</urlset>";
?>
