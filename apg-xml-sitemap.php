<?php
/*
Plugin Name: APG Google Video Sitemap Feed
Version: 3.0.0
Plugin URI: https://wordpress.org/plugins/google-video-sitemap-feed-with-multisite-support/
Description: Dynamically generates a Google Video Sitemap. Compatible with WordPress Multisite installations. Created from <a href="https://profiles.wordpress.org/timbrd/" target="_blank">Tim Brandon</a> <a href="https://wordpress.org/plugins/google-news-sitemap-feed-with-multisite-support/" target="_blank"><strong>Google News Sitemap Feed With Multisite Support</strong></a> and <a href="https://profiles.wordpress.org/labnol/" target="_blank">Amit Agarwal</a> <a href="https://wordpress.org/plugins/xml-sitemaps-for-videos/" target="_blank"><strong>Google XML Sitemap for Videos</strong></a> plugins. Added new functions and ideas (Vimeo and Dailymotion support) by <a href="https://twitter.com/ludobonnet" target="_blank">Ludo Bonnet</a>.
Author: Art Project Group
Author URI: https://artprojectgroup.es/
Requires at least: 6.0
Requires PHP: 7.4
Tested up to: 7.1
License: GPLv3
License URI: https://www.gnu.org/licenses/gpl-3.0.html

Text Domain: google-video-sitemap-feed-with-multisite-support
Domain Path: /languages

@package APG Google Video Sitemap Feed
@category Core
@author Art Project Group
*/

//Igual no deberías poder abrirme
defined( 'ABSPATH' ) || exit;

//Definimos constantes
define( 'APG_VIDEO_SITEMAP_DIRECCION', plugin_basename( __FILE__ ) );
define( 'APG_VIDEO_SITEMAP_VERSION', '3.0.0' );

//Funciones generales de APG
include_once( plugin_dir_path( __FILE__ ) . 'includes/admin/funciones-apg.php' );

//Aviso de sitemap de vídeos duplicado, sólo necesario en el escritorio
if ( is_admin() ) {
    include_once( plugin_dir_path( __FILE__ ) . 'includes/admin/aviso-seo.php' );
}

//Action Scheduler
include_once( plugin_dir_path( __FILE__ ) . 'vendor/autoload.php' );

/**
 * Registra las opciones del plugin.
 *
 * @return void
 */
function apg_video_sitemap_registra_opciones() {
    register_setting( 'apg_video_sitemap_settings_group', 'xml_video_sitemap', [
        'type'              => 'array',
        'sanitize_callback' => 'apg_video_sitemap_sanitize',
        'default'           => [],
    ] );
}
add_action( 'admin_init', 'apg_video_sitemap_registra_opciones' );

/**
 * Sanitiza y valida los campos de configuración.
 *
 * La opción guarda, además de la casilla del formulario, el listado de vídeos ya
 * consultados y los avisos ya enviados, que no viajan en el formulario. Por eso se
 * parte de lo almacenado y sólo se sobreescribe lo que llega, y por eso las claves
 * no pasan por sanitize_key(): los identificadores de vídeo distinguen mayúsculas.
 *
 * @param mixed $input Datos recibidos.
 * @return array
 */
function apg_video_sitemap_sanitize( $input ) {
    $almacenado = get_option( 'xml_video_sitemap' );
    $output     = is_array( $almacenado ) ? $almacenado : [];

    if ( ! is_array( $input ) ) {
        $input = [];
    }

    foreach ( $input as $key => $value ) {
        if ( 'correo' === $key ) {
            continue;
        }

        $key = preg_replace( '/[^A-Za-z0-9_\-]/', '', (string) $key );
        if ( '' === $key ) {
            continue;
        }

        $output[ $key ] = is_scalar( $value ) ? sanitize_text_field( (string) $value ) : '';
    }

    $output[ 'correo' ] = empty( $input[ 'correo' ] ) ? '' : '1';

    return $output;
}

/**
 * Inicializa la opción Google Video Sitemap Feed Options en el menú Ajustes.
 *
 * @return void
 */
function apg_video_sitemap_menu_administrador() {
	add_options_page( __( 'Google Video Sitemap Feed Options.', 'google-video-sitemap-feed-with-multisite-support' ), 'Google Video Sitemap Feed', 'manage_options', 'xml-sitemap-video', 'apg_video_sitemap_formulario' );
}
add_action( 'admin_menu', 'apg_video_sitemap_menu_administrador' );

/**
 * Pinta el formulario de configuración y guarda los campos.
 *
 * @return void
 */
function apg_video_sitemap_formulario() {
    if ( ! current_user_can( 'manage_options' ) ) {
        wp_die( esc_html__( 'You do not have permission to access this page.', 'google-video-sitemap-feed-with-multisite-support' ) );
    }

    include( plugin_dir_path( __FILE__ ) . 'includes/formulario.php' );
}

//Clase
include( plugin_dir_path( __FILE__ ) . 'includes/admin/clases/xml.php' );

//Fuerza la limpieza de Action Scheduler cada mes
add_filter( 'action_scheduler_retention_period', function() {
    return WEEK_IN_SECONDS;
} );

/**
 * Obtiene información de los vídeos publicados vía Action Scheduler.
 *
 * @param string $identificador Identificador del vídeo.
 * @param string $proveedor     Proveedor del vídeo.
 * @return void
 */
function apg_video_sitemap_procesamiento( $identificador, $proveedor ) {
	APG_Video_Sitemap::obtiene_informacion( $identificador, $proveedor );
}
add_action( 'apg_video_sitemap_procesamiento', 'apg_video_sitemap_procesamiento', 10 , 2 );

/**
 * Descarta las cachés cuando cambia la versión del plugin.
 *
 * upgrader_process_complete sólo se dispara al actualizar desde el escritorio. Quien
 * actualice por FTP se quedaría con la caché de la versión anterior, que ya no tiene la
 * estructura que espera este código, así que la versión se comprueba en cada arranque.
 *
 * @return void
 */
function apg_video_sitemap_comprueba_version() {
    if ( get_option( 'apg_video_sitemap_version' ) === APG_VIDEO_SITEMAP_VERSION ) {
        return;
    }

    delete_transient( 'xml_video_sitemap_consulta' );
    delete_transient( 'apg_video_sitemap_plugin' );
    wp_clear_scheduled_hook( 'enviar_ping' ); //El ping a buscadores ya no existe
    update_option( 'apg_video_sitemap_version', APG_VIDEO_SITEMAP_VERSION, false );

    global $wp_rewrite;

    $wp_rewrite->flush_rules(); //Regenera los enlaces permanentes
}
add_action( 'init', 'apg_video_sitemap_comprueba_version', 5 );

/**
 * Controla si se ha actualizado el plugin.
 *
 * @param object $upgrader_object Objeto de actualización.
 * @param array  $opciones        Datos de la actualización.
 * @return void
 */
function apg_video_sitemap_actualiza( $upgrader_object, $opciones ) {
    if ( ! is_array( $opciones ) || ! isset( $opciones[ 'action' ], $opciones[ 'type' ] ) ) {
        return;
    }

    if ( 'update' !== $opciones[ 'action' ] || 'plugin' !== $opciones[ 'type' ] || empty( $opciones[ 'plugins' ] ) ) {
        return;
    }

    if ( ! in_array( APG_VIDEO_SITEMAP_DIRECCION, (array) $opciones[ 'plugins' ], true ) ) {
        return;
    }

    global $wp_rewrite;

    $wp_rewrite->flush_rules(); //Regenera los enlaces permanentes
    delete_option( 'gn-sitemap-video-feed-mu-version' ); //Esta opción ya no es necesaria
    delete_transient( 'xml_video_sitemap_consulta' );
    wp_clear_scheduled_hook( 'enviar_ping' ); //El ping a buscadores ya no existe
}
add_action( 'upgrader_process_complete', 'apg_video_sitemap_actualiza', 10, 2 );

/**
 * Elimina todo rastro del plugin al desinstalarlo.
 *
 * @return void
 */
function apg_video_sitemap_desinstalar() {
	delete_transient( 'apg_video_sitemap_plugin' );
	delete_transient( 'xml_video_sitemap_consulta' );
    delete_transient( 'xml_video_sitemap_procesado' );
    delete_transient( 'xml_video_sitemap' );

    $configuracion = get_option( 'xml_video_sitemap' );
    if ( is_array( $configuracion ) ) {
        foreach ( $configuracion as $clave => $url ) {
            //La caché de cada vídeo se guarda bajo el hash de su URL, no bajo la URL.
            if ( 'correo' === $clave || ! is_string( $url ) || 0 !== strpos( $url, 'http' ) ) {
                continue;
            }
            delete_transient( 'apg_video_' . md5( $url ) );
        }
    }

    wp_clear_scheduled_hook( 'enviar_ping' );
    delete_metadata( 'user', 0, 'apg_video_sitemap_aviso_descartado', '', true );

    if ( function_exists( 'as_unschedule_all_actions' ) ) {
        as_unschedule_all_actions( 'apg_video_sitemap_procesamiento' );
    }

	delete_option( 'xml_video_sitemap' );
    delete_option( 'apg_video_sitemap_paginas' );
    delete_option( 'apg_video_sitemap_version' );
}
register_uninstall_hook( __FILE__, 'apg_video_sitemap_desinstalar' );

/**
 * Controla la desactivación del plugin.
 *
 * @return void
 */
function apg_video_sitemap_desactivador() {
    APG_Video_Sitemap::desactivar();
}
register_deactivation_hook( __FILE__, 'apg_video_sitemap_desactivador' );
