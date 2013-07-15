<?php
/**
 * XML Sitemap Feed Template for displaying an XML Sitemap feed.
 *
 * @package Google Video Sitemap Feed With Multisite Support plugin for WordPress
 */

# given a video id, get the duration.
# might give this a delay to avoid running into issues with YouTube.
function youtube_duration ($id) {
	try {
		$ch = curl_init ();
        curl_setopt ($ch, CURLOPT_URL, "http://gdata.youtube.com/feeds/api/videos/$id");
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec ($ch);
        curl_close ($ch);

        preg_match ("/duration=['\"]([0-9]*)['\"]/", $data, $match);
        return $match [1];

    } catch (Exception $e) {
        # returning 0 if the YouTube API fails for some reason.
        return '0';
	}
}

status_header('200'); // force header('HTTP/1.1 200 OK') for sites without posts
header('Content-Type: text/xml; charset=' . get_bloginfo('charset'), true);

echo '<?xml version="1.0" encoding="'.get_bloginfo('charset').'"?>
<!-- Created by Art Project Group (http://www.artprojectgroup.es/) -->
<!-- Generated-on="'.date('Y-m-d\TH:i:s+00:00').'" -->
<?xml-stylesheet type="text/xsl" href="' . get_bloginfo('wpurl') . '/wp-content/plugins/google-video-sitemap-feed-with-multisite-support/video-sitemap.xsl"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:video="http://www.google.com/schemas/sitemap-video/1.1">'."\n";

// Request
$posts = $wpdb->get_results ("SELECT id, post_title, post_content, post_date_gmt, post_excerpt
                            FROM $wpdb->posts WHERE post_status = 'publish'
                            AND (post_type = 'post' OR post_type = 'page')
                            AND (post_content LIKE '%youtube.com%' OR post_content LIKE '%youtube-nocookie.com%')
                            ORDER BY post_date DESC");
	
global $wp_query;
$wp_query->is_404 = false;	// force is_404() condition to false when on site without posts
$wp_query->is_feed = true;	// force is_feed() condition to true so WP Super Cache includes the sitemap in its feeds cache

if (!empty ($posts)) {
	$videos = array();
	foreach ($posts as $post) {
		$c = 0;
		if (preg_match_all ('/youtube.com\/(v\/|watch\?v=|embed\/)([a-zA-Z0-9\-_]*)/', $post->post_content, $matches, PREG_SET_ORDER) || preg_match_all ('/youtube-nocookie.com\/(v\/|watch\?v=|embed\/)([a-zA-Z0-9\-_]*)/', $post->post_content, $matches, PREG_SET_ORDER)) {
			$excerpt = ($post->post_excerpt != "") ? $post->post_excerpt : $post->post_title; 
			$permalink = htmlspecialchars(get_permalink($post->id)); 

			foreach ($matches as $match) {
                                    
				$id = $match [2];
				$fix =  $c++==0?'':' [Video '. $c .'] ';
                        
				if (in_array($id, $videos)) continue;
                            
				array_push($videos, $id);
                        
				echo "\t".'<url>'."\n";
                echo "\t\t".'<loc>'.$permalink.'</loc>'."\n";
				echo "\t\t".'<video:video>'."\n";
                echo "\t\t".'<video:player_loc allow_embed="yes" autoplay="autoplay=1">http://www.youtube.com/v/'.$id.'</video:player_loc>'."\n";
                echo "\t\t".'<video:thumbnail_loc>http://i.ytimg.com/vi/'.$id.'/hqdefault.jpg</video:thumbnail_loc>'."\n";
                echo "\t\t".'<video:title>' . htmlspecialchars($post->post_title) . $fix . '</video:title>'."\n";
                echo "\t\t".'<video:description>' . $fix . htmlspecialchars($excerpt) . '</video:description>'."\n";
    
                if ($_POST['time'] == 1) {  
                	$duration = youtube_duration ($id);
                	if ($duration != 0) echo "\t\t".'<video:duration>'.youtube_duration ($id).'</video:duration>'."\n";
                }

                echo "\t\t".'<video:publication_date>'.date (DATE_W3C, strtotime ($post->post_date_gmt)).'</video:publication_date>'."\n";
    
                $posttags = get_the_tags($post->id); 
				if ($posttags) { 
                	$tagcount=0;
                	foreach ($posttags as $tag) {
                		if ($tagcount++ > 32) break;
                		echo "\t\t".'<video:tag>'.$tag->name.'</video:tag>'."\n";
                	}
                }    

                $postcats = get_the_category($post->id); 
				if ($postcats) { 
                	foreach ($postcats as $category) {
                		echo "\t\t".'<video:category>'.$category->name.'</video:category>'."\n";
                		break;
                	}
                }        

				echo "\t\t".'</video:video>'."\n";
				echo "\t".'</url>'."\n";
			}
		}
	}
}

echo "</urlset>";
?>
