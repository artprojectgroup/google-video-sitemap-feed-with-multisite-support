<?php
/**
 * Ayuda contextual de la página de ajustes: la pestaña «Ayuda» nativa de WordPress, con un panel
 * por cada bloque de la pantalla. La versión completa vive en la web y se enlaza desde el lateral.
 *
 * @package APG Google Video Sitemap Feed
 */

defined( 'ABSPATH' ) || exit;

/**
 * Añade los paneles de ayuda a la pantalla de ajustes.
 */
class APG_Video_Sitemap_Help {

	/**
	 * Dirección de la documentación completa, en español.
	 */
	const DOCS_URL = 'https://artprojectgroup.es/documentacion/apg-google-video-sitemap-feed';

	/**
	 * Dirección de la documentación completa, en inglés.
	 */
	const DOCS_URL_EN = 'https://artprojectgroup.com/documentation/apg-google-video-sitemap-feed';

	/**
	 * Sustituye la ayuda de la pantalla de ajustes por los paneles del plugin.
	 *
	 * La pantalla es de este plugin y no tiene pestañas, así que se añaden los dos paneles de una
	 * vez. Se engancha en load-{hook de la página de ajustes}.
	 *
	 * @return void
	 */
	public static function add_tabs() {
		$screen = get_current_screen();
		if ( ! $screen ) {
			return;
		}

		$screen->remove_help_tabs();

		foreach ( self::get_panels() as $id => $panel ) {
			$screen->add_help_tab(
				array(
					'id'      => 'apg_video_sitemap_help_' . $id,
					'title'   => $panel['title'],
					'content' => self::render( $panel['items'] ),
				)
			);
		}

		$screen->set_help_sidebar(
			'<p><strong>' . esc_html__( 'More help', 'google-video-sitemap-feed-with-multisite-support' ) . '</strong></p>' .
			'<p><a href="' . esc_url( self::documentacion() ) . '" target="_blank">' . esc_html__( 'Full documentation', 'google-video-sitemap-feed-with-multisite-support' ) . '</a></p>'
		);
	}

	/**
	 * Dirección de la documentación en el idioma de quien lee la pantalla.
	 *
	 * @return string
	 */
	private static function documentacion() {
		return ( 0 === strpos( get_user_locale(), 'es' ) ) ? self::DOCS_URL : self::DOCS_URL_EN;
	}

	/**
	 * Pinta un panel: un párrafo de entrada y una lista de término => explicación.
	 *
	 * @param array $items Primer elemento: texto de entrada (cadena). Después, término => explicación.
	 * @return string
	 */
	private static function render( array $items ) {
		$html = '';
		$list = '';

		foreach ( $items as $term => $text ) {
			if ( is_int( $term ) ) {
				$html .= '<p>' . esc_html( $text ) . '</p>';
			} else {
				$list .= '<li><strong>' . esc_html( $term ) . '</strong>: ' . esc_html( $text ) . '</li>';
			}
		}

		return $html . ( '' !== $list ? '<ul>' . $list . '</ul>' : '' );
	}

	/**
	 * Paneles de la pantalla de ajustes.
	 *
	 * @return array ID del panel => array( title, items ).
	 */
	private static function get_panels() {
		return array(
			'sitemap'       => array(
				'title' => __( 'The sitemap', 'google-video-sitemap-feed-with-multisite-support' ),
				'items' => array(
					__( 'The plugin builds the video sitemap on the fly, every time it is asked for, so there is no file to regenerate and nothing to keep up to date by hand. Publish a post with a video and it is in the sitemap straight away.', 'google-video-sitemap-feed-with-multisite-support' ),
					__( 'Where it is', 'google-video-sitemap-feed-with-multisite-support' )      => __( 'at /sitemap-video.xml of your site. That address is an index that points to /sitemap-video-1.xml, -2 and so on, because the videos are served in pages. It is the address you give to Google Search Console.', 'google-video-sitemap-feed-with-multisite-support' ),
					__( 'What goes in it', 'google-video-sitemap-feed-with-multisite-support' )  => __( 'the YouTube, Vimeo and Dailymotion videos embedded in your published content, and the ones hosted on your own site through the video block, the video shortcode or the media library, each with the title, the description, the thumbnail, the duration and the date Google asks for. It looks in every post type, and also inside the custom fields of the usual page builders and the oEmbed cache, so a video put in by the builder is not left out.', 'google-video-sitemap-feed-with-multisite-support' ),
					__( 'One entry per page', 'google-video-sitemap-feed-with-multisite-support' ) => __( 'every video on the same page is grouped into a single sitemap entry, which is what the Google specification expects. A page with five videos is one entry with five videos, not five entries.', 'google-video-sitemap-feed-with-multisite-support' ),
					__( 'On a network', 'google-video-sitemap-feed-with-multisite-support' )     => __( 'each site of a Multisite network has its own sitemap at its own address, and each one is submitted separately.', 'google-video-sitemap-feed-with-multisite-support' ),
				),
			),

			'notifications' => array(
				'title' => __( 'Notifications', 'google-video-sitemap-feed-with-multisite-support' ),
				'items' => array(
					__( 'A video that has been deleted, made private or blocked where you sell stays embedded in your post, looking fine to you and broken to everyone else. The plugin checks them and can tell you.', 'google-video-sitemap-feed-with-multisite-support' ),
					__( 'Send video error notifications by email', 'google-video-sitemap-feed-with-multisite-support' ) => __( 'writes to the site administrator when a video is no longer available, so you can replace it. It writes once per video, never again for the same one, and a network error or a server failure does not count as a missing video, because those come back on their own.', 'google-video-sitemap-feed-with-multisite-support' ),
				),
			),
		);
	}
}
