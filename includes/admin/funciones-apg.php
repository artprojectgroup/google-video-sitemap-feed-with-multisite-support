<?php
//Igual no deberías poder abrirme
defined( 'ABSPATH' ) || exit;

//Definimos las variables
$apg_video_sitemap = [ 	
	'plugin' 		=> 'APG Google Video Sitemap Feed', 
	'plugin_uri' 	=> 'google-video-sitemap-feed-with-multisite-support', 
	'donacion' 		=> 'https://artprojectgroup.es/tienda/donacion',
	'soporte' 		=> 'https://artprojectgroup.es/tienda/soporte-tecnico',
	'plugin_url' 	=> 'https://artprojectgroup.es/plugins-para-wordpress/apg-google-video-sitemap-feed', 
	'ajustes' 		=> 'options-general.php?page=xml-sitemap-video', 
	'puntuacion' 	=> 'https://wordpress.org/support/view/plugin-reviews/google-video-sitemap-feed-with-multisite-support'
 ];

//Número máximo de páginas por feed
define( 'APG_VIDEO_SITEMAP_MAXIMO', 50000 );

/**
 * Devuelve los textos de los enlaces de cortesía, ya traducidos.
 *
 * Se agrupan aquí para que cada cadena con marcador pueda llevar su propio comentario
 * para quien traduce, que es lo que exige la revisión de WordPress.org.
 *
 * @return string[]
 */
function apg_video_sitemap_dame_textos() {
	/* translators: %s: name of the payment platform. */
	$donacion	= sprintf( __( 'Make a donation by %s', 'google-video-sitemap-feed-with-multisite-support' ), 'APG' );
	/* translators: %s: name of the social network. */
	$facebook	= sprintf( __( 'Follow us on %s', 'google-video-sitemap-feed-with-multisite-support' ), 'Facebook' );
	/* translators: %s: name of the social network. */
	$twitter	= sprintf( __( 'Follow us on %s', 'google-video-sitemap-feed-with-multisite-support' ), 'Twitter' );
	/* translators: %s: name of the social network. */
	$linkedin	= sprintf( __( 'Follow us on %s', 'google-video-sitemap-feed-with-multisite-support' ), 'LinkedIn' );
	/* translators: %s: name of the website where the plugins are published. */
	$plugins	= sprintf( __( 'More plugins on %s', 'google-video-sitemap-feed-with-multisite-support' ), 'WordPress' );
	/* translators: %s: name of the contact method. */
	$correo		= sprintf( __( 'Contact us by %s', 'google-video-sitemap-feed-with-multisite-support' ), 'e-mail' );
	/* translators: %s: name of the contact method. */
	$skype		= sprintf( __( 'Contact us by %s', 'google-video-sitemap-feed-with-multisite-support' ), 'Skype' );

	return [
		'donacion'	=> $donacion,
		'facebook'	=> $facebook,
		'twitter'	=> $twitter,
		'linkedin'	=> $linkedin,
		'plugins'	=> $plugins,
		'correo'	=> $correo,
		'skype'		=> $skype,
	];
}

//Enlaces adicionales personalizados
function apg_video_sitemap_enlaces( $enlaces, $archivo ) {
	global $apg_video_sitemap;

	if ( $archivo !== APG_VIDEO_SITEMAP_DIRECCION ) {
		return $enlaces;
	}

	$textos		= apg_video_sitemap_dame_textos();
	$enlaces[]	= '<a href="' . esc_url( $apg_video_sitemap[ 'donacion' ] ) . '" target="_blank" title="' . esc_attr( $textos[ 'donacion' ] ) . '"><span class="genericon genericon-cart"></span></a>';
	$enlaces[]	= '<a href="' . esc_url( $apg_video_sitemap[ 'plugin_url' ] ) . '" target="_blank" title="' . esc_attr( $apg_video_sitemap[ 'plugin' ] ) . '"><strong class="artprojectgroup">APG</strong></a>';
	$enlaces[]	= '<a href="' . esc_url( 'https://www.facebook.com/artprojectgroup' ) . '" title="' . esc_attr( $textos[ 'facebook' ] ) . '" target="_blank"><span class="genericon genericon-facebook-alt"></span></a> <a href="' . esc_url( 'https://twitter.com/artprojectgroup' ) . '" title="' . esc_attr( $textos[ 'twitter' ] ) . '" target="_blank"><span class="genericon genericon-twitter"></span></a> <a href="' . esc_url( 'https://es.linkedin.com/in/artprojectgroup' ) . '" title="' . esc_attr( $textos[ 'linkedin' ] ) . '" target="_blank"><span class="genericon genericon-linkedin"></span></a>';
	$enlaces[]	= '<a href="' . esc_url( 'https://profiles.wordpress.org/artprojectgroup/' ) . '" title="' . esc_attr( $textos[ 'plugins' ] ) . '" target="_blank"><span class="genericon genericon-wordpress"></span></a>';
	$enlaces[]	= '<a href="' . esc_url( 'mailto:info@artprojectgroup.es' ) . '" title="' . esc_attr( $textos[ 'correo' ] ) . '"><span class="genericon genericon-mail"></span></a> <a href="' . esc_url( 'skype:artprojectgroup' ) . '" title="' . esc_attr( $textos[ 'skype' ] ) . '"><span class="genericon genericon-skype"></span></a>';
	$enlaces[]	= apg_video_sitemap_plugin( $apg_video_sitemap[ 'plugin_uri' ] );

	return $enlaces;
}
add_filter( 'plugin_row_meta', 'apg_video_sitemap_enlaces', 10, 2 );

//Añade el botón de configuración
function apg_video_sitemap_enlace_de_ajustes( $enlaces ) { 
	global $apg_video_sitemap;

	/* translators: %s: name of the plugin. */
	$ajustes			= sprintf( __( 'Settings of %s', 'google-video-sitemap-feed-with-multisite-support' ), $apg_video_sitemap[ 'plugin' ] );
	/* translators: %s: name of the plugin. */
	$soporte			= sprintf( __( 'Support of %s', 'google-video-sitemap-feed-with-multisite-support' ), $apg_video_sitemap[ 'plugin' ] );
	$enlaces_de_ajustes	= [
		'<a href="' . esc_url( admin_url( $apg_video_sitemap[ 'ajustes' ] ) ) . '" title="' . esc_attr( $ajustes ) . '">' . esc_html__( 'Settings', 'google-video-sitemap-feed-with-multisite-support' ) . '</a>', 
		'<a href="' . esc_url( $apg_video_sitemap[ 'soporte' ] ) . '" title="' . esc_attr( $soporte ) . '" target="_blank">' . esc_html__( 'Support', 'google-video-sitemap-feed-with-multisite-support' ) . '</a>'
	];
	foreach ( $enlaces_de_ajustes as $enlace_de_ajustes )	{
		array_unshift( $enlaces, $enlace_de_ajustes );
	}
	
	return $enlaces; 
}
$plugin = APG_VIDEO_SITEMAP_DIRECCION; 
add_filter( "plugin_action_links_$plugin", 'apg_video_sitemap_enlace_de_ajustes' );

/**
 * Obtiene la valoración del plugin en WordPress.org.
 *
 * Devuelve HTML simple ya escapado: quien lo imprima debe pasarlo por wp_kses_post().
 *
 * @param string $nombre Slug del plugin en WordPress.org.
 * @return string
 */
function apg_video_sitemap_plugin( $nombre ) {
	global $apg_video_sitemap;

	$enlace		= esc_url( $apg_video_sitemap[ 'puntuacion' ] . '?rate=5#postform' );
	/* translators: %s: name of the plugin. */
	$titulo		= esc_attr( sprintf( __( 'Please, rate %s:', 'google-video-sitemap-feed-with-multisite-support' ), $apg_video_sitemap[ 'plugin' ] ) );
	$respuesta	= get_transient( 'apg_video_sitemap_plugin' );
	if ( $respuesta === false ) {
		//El slug viaja como parámetro de la API: se codifica, nunca se pasa por esc_url().
		$respuesta = wp_remote_get( 'https://api.wordpress.org/plugins/info/1.2/?action=plugin_information&request[slug]=' . rawurlencode( $nombre ), [ 'timeout' => 10 ] );
		//Un fallo de red no se cachea un día entero: se reintenta pronto.
		set_transient( 'apg_video_sitemap_plugin', $respuesta, is_wp_error( $respuesta ) ? HOUR_IN_SECONDS : DAY_IN_SECONDS );
	}

	$plugin = is_wp_error( $respuesta ) ? null : json_decode( wp_remote_retrieve_body( $respuesta ) );
	if ( ! is_object( $plugin ) || ! isset( $plugin->rating, $plugin->num_ratings ) ) {
		return '<a title="' . $titulo . '" href="' . $enlace . '" class="estrellas">' . esc_html__( 'Unknown rating', 'google-video-sitemap-feed-with-multisite-support' ) . '</a>';
	}

	ob_start();
	wp_star_rating( [
		'rating'	=> (float) $plugin->rating,
		'type'		=> 'percent',
		'number'	=> (int) $plugin->num_ratings,
	] );
	$estrellas = ob_get_clean();

	return '<a title="' . $titulo . '" href="' . $enlace . '" class="estrellas">' . $estrellas . '</a>';
}

//Hoja de estilo
function apg_video_sitemap_estilo( $pantalla ) {
	if ( ! in_array( $pantalla, [ 'settings_page_xml-sitemap-video', 'plugins.php', 'plugins-network.php' ], true ) ) {
		return;
	}
	wp_enqueue_style( 'apg_video_sitemap_hoja_de_estilo', plugins_url( 'assets/css/style.css', APG_VIDEO_SITEMAP_DIRECCION ), [], APG_VIDEO_SITEMAP_VERSION );
}
add_action( 'admin_enqueue_scripts', 'apg_video_sitemap_estilo' );
