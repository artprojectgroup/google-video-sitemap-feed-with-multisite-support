<?php
/*
Plugin Name: Google Video Sitemap Feed With Multisite Support
Version: 1.5
Plugin URI: http://wordpress.org/plugins/google-video-sitemap-feed-with-multisite-support/
Description: Dynamically generates a Google Video Sitemap and automatically submit updates to Google and Bing. Compatible with WordPress Multisite installations. Created from <a href="http://profiles.wordpress.org/users/timbrd/" target="_blank">Tim Brandon</a> <a href="http://wordpress.org/plugins/google-news-sitemap-feed-with-multisite-support/" target="_blank"><strong>Google News Sitemap Feed With Multisite Support</strong></a> and <a href="http://profiles.wordpress.org/labnol/" target="_blank">Amit Agarwal</a> <a href="http://wordpress.org/plugins/xml-sitemaps-for-videos/" target="_blank"><strong>Google XML Sitemap for Videos</strong></a> plugins. Added new functions and ideas (Vimeo and Dailymotion support) by <a href="https://twitter.com/ludobonnet" target="_blank">Ludo Bonnet</a>.
Author: Art Project Group
Author URI: http://www.artprojectgroup.es/

Requires at least: 2.6
Tested up to: 4.3

Text Domain: xml_video_sitemap
Domain Path: /i18n/languages

@package Google Video Sitemap Feed With Multisite Support
@category Core
@author Art Project Group
*/

/* --------------------
 *  AVAILABLE HOOKS
 * --------------------
 *
 * FILTERS
 *	xml_sitemap_url	->	Filters the URL used in the sitemap reference in robots.txt
 *				( receives an ARRAY and MUST return one; can be multiple urls ) 
 *				and for the home URL in the sitemap ( receives a STRING and MUST )
 *				return one ) itself. Useful for multi language plugins or other 
 *				plugins that affect the blogs main URL... See pre-defined filter
 *				XMLSitemapVideoFeed::qtranslate() in XMLSitemapVideoFeed.class.php as an
 *				example.
 * ACTIONS
 *	[ none at this point, but feel free to request, suggest or code one : ) ]
 *	
 */

//Igual no deberías poder abrirme
if ( !defined( 'ABSPATH' ) ) {
    exit;
}

//Definimos constantes
define( 'DIRECCION_xml_video_sitemap', plugin_basename( __FILE__ ) );

//Definimos las variables
$xml_video_sitemap = array( 	
	'plugin' 		=> 'Google Video Sitemap Feed With Multisite Support', 
	'plugin_uri' 	=> 'google-video-sitemap-feed-with-multisite-support', 
	'donacion' 		=> 'http://www.artprojectgroup.es/tienda/donacion',
	'soporte' 		=> 'http://www.artprojectgroup.es/tienda/soporte-tecnico',
	'plugin_url' 	=> 'http://www.artprojectgroup.es/plugins-para-wordpress/google-video-sitemap-feed-with-multisite-support', 
	'ajustes' 		=> 'options-general.php?page=xml-sitemap-video', 
	'puntuacion' 	=> 'http://wordpress.org/support/view/plugin-reviews/google-video-sitemap-feed-with-multisite-support'
 );

//Carga el idioma
load_plugin_textdomain( 'xml_video_sitemap', null, dirname( DIRECCION_xml_video_sitemap ) . '/i18n/languages' );

//Enlaces adicionales personalizados
function xml_video_sitemap_enlaces( $enlaces, $archivo ) {
	global $xml_video_sitemap;

	if ( $archivo == DIRECCION_xml_video_sitemap ) {
		$enlaces[] = '<a href="' . $xml_video_sitemap['donacion'] . '" target="_blank" title="' . __( 'Make a donation by ', 'xml_video_sitemap' ) . 'APG"><span class="genericon genericon-cart"></span></a>';
		$enlaces[] = '<a href="'. $xml_video_sitemap['plugin_url'] . '" target="_blank" title="' . $xml_video_sitemap['plugin'] . '"><strong class="artprojectgroup">APG</strong></a>';
		$enlaces[] = '<a href="https://www.facebook.com/artprojectgroup" title="' . __( 'Follow us on ', 'xml_video_sitemap' ) . 'Facebook" target="_blank"><span class="genericon genericon-facebook-alt"></span></a> <a href="https://twitter.com/artprojectgroup" title="' . __( 'Follow us on ', 'xml_video_sitemap' ) . 'Twitter" target="_blank"><span class="genericon genericon-twitter"></span></a> <a href="https://plus.google.com/+ArtProjectGroupES" title="' . __( 'Follow us on ', 'xml_video_sitemap' ) . 'Google+" target="_blank"><span class="genericon genericon-googleplus-alt"></span></a> <a href="http://es.linkedin.com/in/artprojectgroup" title="' . __( 'Follow us on ', 'xml_video_sitemap' ) . 'LinkedIn" target="_blank"><span class="genericon genericon-linkedin"></span></a>';
		$enlaces[] = '<a href="http://profiles.wordpress.org/artprojectgroup/" title="' . __( 'More plugins on ', 'xml_video_sitemap' ) . 'WordPress" target="_blank"><span class="genericon genericon-wordpress"></span></a>';
		$enlaces[] = '<a href="mailto:info@artprojectgroup.es" title="' . __( 'Contact with us by ', 'xml_video_sitemap' ) . 'e-mail"><span class="genericon genericon-mail"></span></a> <a href="skype:artprojectgroup" title="' . __( 'Contact with us by ', 'xml_video_sitemap' ) . 'Skype"><span class="genericon genericon-skype"></span></a>';
		$enlaces[] = xml_video_sitemap_plugin( $xml_video_sitemap['plugin_uri'] );
	}
	
	return $enlaces;
}
add_filter( 'plugin_row_meta', 'xml_video_sitemap_enlaces', 10, 2 );

//Añade el botón de configuración
function xml_video_sitemap_enlace_de_ajustes( $enlaces ) { 
	global $xml_video_sitemap;

	$enlaces_de_ajustes = array(
		'<a href="' . $xml_video_sitemap['ajustes'] . '" title="' . __( 'Settings of ', 'xml_video_sitemap' ) . $xml_video_sitemap['plugin'] .'">' . __( 'Settings', 'xml_video_sitemap' ) . '</a>', 
		'<a href="' . $xml_video_sitemap['soporte'] . '" title="' . __( 'Support of ', 'xml_video_sitemap' ) . $xml_video_sitemap['plugin'] .'">' . __( 'Support', 'apg_shipping' ) . '</a>'
	);
	foreach( $enlaces_de_ajustes as $enlace_de_ajustes )	{
		array_unshift( $enlaces, $enlace_de_ajustes );
	}
	
	return $enlaces; 
}
$plugin = DIRECCION_xml_video_sitemap; 
add_filter( "plugin_action_links_$plugin", 'xml_video_sitemap_enlace_de_ajustes' );

//Inicializa la opción Google Video Sitemap Feed Options en el menú Ajustes
function xml_sitemap_video_menu_administrador() {
	add_options_page( __( 'Google Video Sitemap Feed Options.', 'xml_video_sitemap' ), 'Google Video Sitemap Feed', 'manage_options', 'xml-sitemap-video', 'xml_sitemap_video_formulario_de_configuracion' );
}
add_action( 'admin_menu', 'xml_sitemap_video_menu_administrador' );

//Pinta el formulario de configuración
function xml_sitemap_video_formulario_de_configuracion() {
	$actualizacion = false;
	
	$campos = array( 'correo' );
	foreach ( $campos as $campo ) {
		if ( isset( $_POST[$campo] ) ) {
			$actualizacion = true;
		}
	}
		
	if ($actualizacion) {
		$campos_chequeo = array( 'correo' );
		foreach ( $campos_chequeo as $campo ) {
			if ( !isset( $_POST[$campo] ) ) {
				$_POST[$campo] = 0;
			}
		}
		
		$configuracion = array();
		foreach ( $campos as $campo ) {
			$configuracion[$campo] = $_POST[$campo];
		}
			
		if ( get_option( 'xml_video_sitemap' ) || get_option( 'xml_video_sitemap' ) == NULL ) {
			update_option('xml_video_sitemap', $configuracion );
		} else {
			add_option('xml_video_sitemap', $configuracion );
		}
	}
	include( 'includes/formulario.php' );
}

//Constantes
define( 'XMLSVF_VERSION', '1.5' );
define( 'XMLSVF_MEMORY_LIMIT', '128M' );

if ( file_exists( dirname( __FILE__ ) . '/google-video-sitemap-feed-mu' ) ) {
	define( 'XMLSVF_PLUGIN_DIR', dirname( __FILE__ ) . '/google-video-sitemap-feed-mu' );
} else {
	define( 'XMLSVF_PLUGIN_DIR', dirname( __FILE__ ) );		
}

//Clase
if ( class_exists( 'XMLSitemapVideoFeed' ) || include( XMLSVF_PLUGIN_DIR . '/includes/XMLSitemapVideoFeed.class.php' ) ) {
	XMLSitemapVideoFeed::go();
}

//Obtiene toda la información sobre el plugin
function xml_video_sitemap_plugin( $nombre ) {
	global $xml_video_sitemap;
	
	$argumentos = ( object ) array( 
		'slug' => $nombre 
	);
	$consulta = array( 
		'action' => 'plugin_information', 
		'timeout' => 15, 
		'request' => serialize( $argumentos )
	);
	$respuesta = get_transient( 'xml_video_sitemap_plugin' );
	if ( false === $respuesta ) {
		$respuesta = wp_remote_post( 'http://api.wordpress.org/plugins/info/1.0/', array( 
			'body' => $consulta)
		);
		set_transient( 'xml_video_sitemap_plugin', $respuesta, 24 * HOUR_IN_SECONDS );
	}
	if ( !is_wp_error( $respuesta ) ) {
		$plugin = get_object_vars( unserialize( $respuesta['body'] ) );
	} else {
		$plugin['rating'] = 100;
	}

	$rating = array(
	   'rating'	=> $plugin['rating'],
	   'type'	=> 'percent',
	   'number'	=> $plugin['num_ratings'],
	);
	ob_start();
	wp_star_rating( $rating );
	$estrellas = ob_get_contents();
	ob_end_clean();

	return '<a title="' . sprintf( __( 'Please, rate %s:', 'xml_video_sitemap' ), $xml_video_sitemap['plugin'] ) . '" href="' . $xml_video_sitemap['puntuacion'] . '?rate=5#postform" class="estrellas">' . $estrellas . '</a>';
}

//Muestra el mensaje de actualización
function xml_video_sitemap_actualizacion() {
	global $xml_video_sitemap;
	
    echo '<div class="error fade" id="message"><h3>' . $xml_video_sitemap['plugin'] . '</h3><h4>' . sprintf( __( "Please, update your %s. It's very important!", 'xml_video_sitemap' ), '<a href="' . $xml_video_sitemap['ajustes'] . '" title="' . __( 'Settings', 'xml_video_sitemap' ) . '">' . __('settings', 'xml_video_sitemap') . '</a>') . '</h4></div>';
}

//Carga las hojas de estilo
function xml_video_sitemap_muestra_mensaje() {
	wp_register_style( 'xml_video_sitemap_hoja_de_estilo', plugins_url( 'assets/css/style.css', __FILE__ ) ); //Carga la hoja de estilo
	wp_enqueue_style( 'xml_video_sitemap_hoja_de_estilo' ); //Carga la hoja de estilo global
	wp_register_style( 'xml_video_sitemap_fuentes', plugins_url( 'assets/fonts/stylesheet.css', __FILE__ ) ); //Carga la hoja de estilo global
	wp_enqueue_style( 'xml_video_sitemap_fuentes' ); //Carga la hoja de estilo global
	
	$configuracion = get_option( 'xml_video_sitemap' );
	if ( !isset( $configuracion['correo'] ) ) {
		add_action( 'admin_notices', 'xml_video_sitemap_actualizacion' ); //Comprueba si hay que mostrar el mensaje de actualización
	}
}
add_action( 'admin_init', 'xml_video_sitemap_muestra_mensaje' );

//Eliminamos todo rastro del plugin al desinstalarlo
function xml_video_sitemap_desinstalar() {
	delete_option( 'gn-sitemap-video-feed-mu-version' );
	delete_transient( 'xml_video_sitemap_plugin' );
	delete_transient( 'xml_sitemap_video' );
	delete_transient( 'xml_sitemap_video_consulta' );
	$configuracion = get_option( 'xml_sitemap_video' );
	foreach ($configuracion as $url) {
		delete_transient($url);
	}
	delete_option( 'xml_sitemap_video' );
}
register_uninstall_hook( __FILE__, 'xml_video_sitemap_desinstalar' );
