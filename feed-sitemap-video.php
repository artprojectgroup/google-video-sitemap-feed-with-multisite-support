<?php
/**
 * XML Sitemap Feed Template for displaying an XML Sitemap feed.
 *
 * @package Google Video Sitemap Feed With Multisite Support plugin for WordPress
 */

//Obtiene la duración del vídeo
function duracion_del_video($identificador) {
	try 
	{
		$ch = curl_init ();
		curl_setopt ($ch, CURLOPT_URL, "http://gdata.youtube.com/feeds/api/videos/$identificador");
		curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
		$youtube = curl_exec ($ch);
		curl_close ($ch);

		preg_match ("/duration=['\"]([0-9]*)['\"]/", $youtube, $duracion);
		
		return $duracion[1];
    } 
	catch (Exception $e) 
	{
		return '0'; # returning 0 if the YouTube API fails for some reason.
	}
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
		if (preg_match_all ('/youtube.com\/(v\/|watch\?v=|embed\/)([^\$][a-zA-Z0-9\-_]*)/', $entrada->post_content, $videos, PREG_SET_ORDER) || preg_match_all ('/youtube-nocookie.com\/(v\/|watch\?v=|embed\/)([^\$][a-zA-Z0-9\-_]*)/', $entrada->post_content, $videos, PREG_SET_ORDER)) 
		{
			$extracto = ($entrada->post_excerpt != "") ? $entrada->post_excerpt : $entrada->post_title; 
			$enlace = htmlspecialchars(get_permalink($entrada->id)); 

			foreach ($videos as $video) 
			{
				$identificador = $video[2];
                        
				if (in_array($identificador, $videos)) continue;
                            
				array_push($videos, $identificador);
                        
				echo "\t" . '<url>' . "\n";
				echo "\t\t" . '<loc>' . $enlace . '</loc>' . "\n";
				echo "\t\t" . '<video:video>' . "\n";
				echo "\t\t" . '<video:player_loc allow_embed="yes" autoplay="autoplay=1">http://www.youtube.com/v/' . $identificador . '</video:player_loc>' . "\n";
				echo "\t\t" . '<video:thumbnail_loc>http://i.ytimg.com/vi/' . $identificador . '/hqdefault.jpg</video:thumbnail_loc>' . "\n";
				echo "\t\t" . '<video:title>' . htmlspecialchars($entrada->post_title) . '</video:title>' . "\n";
				echo "\t\t" . '<video:description>' . htmlspecialchars($extracto) . '</video:description>' . "\n";
    
				if ($_POST['time'] == 1) 
				{  
                	$duracion = duracion_del_video($identificador);
                	if ($duracion != 0) echo "\t\t" . '<video:duration>' . duracion_del_video($identificador) . '</video:duration>' . "\n";
				}
				echo "\t\t" . '<video:publication_date>' . date(DATE_W3C, strtotime($entrada->post_date_gmt)) . '</video:publication_date>' . "\n";
    
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
