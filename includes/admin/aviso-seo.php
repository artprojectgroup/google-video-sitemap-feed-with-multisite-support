<?php
//Igual no deberías poder abrirme
defined( 'ABSPATH' ) || exit;

/**
 * Detecta si un plugin SEO ya está publicando un sitemap de vídeos.
 *
 * A diferencia del sitemap de imágenes, el de vídeos es una función de pago en los cuatro
 * plugins, así que no basta con que el plugin esté activo: hay que confirmar que la función
 * concreta está encendida. Ante la duda no se dice nada, porque un aviso equivocado llevaría
 * a desactivar el único sitemap de vídeos que tiene el sitio web.
 *
 * Las claves de cada plugin están verificadas contra los importadores de Rank Math, que leen
 * la configuración de sus competidores: seo-by-rank-math/includes/admin/importers/.
 *
 * @return string Nombre del plugin detectado, o cadena vacía.
 */
function apg_video_sitemap_detecta_seo() {
	//Rank Math: el sitemap de vídeos es un módulo propio, sólo disponible en la versión Pro
	if ( defined( 'RANK_MATH_VERSION' ) ) {
		$modulos = (array) get_option( 'rank_math_modules', [] );
		if ( in_array( 'sitemap', $modulos, true ) && in_array( 'video-sitemap', $modulos, true ) ) {
			return 'Rank Math SEO';
		}
	}

	//Yoast SEO: el complemento Video SEO guarda su configuración en su propia opción
	if ( defined( 'WPSEO_VERSION' ) && class_exists( 'WPSEO_Options' ) ) {
		if ( WPSEO_Options::get( 'enable_xml_sitemap', false ) && ! empty( get_option( 'wpseo_video' ) ) ) {
			return 'Yoast SEO';
		}
	}

	//All in One SEO: la configuración de pago viaja en un campo JSON aparte
	if ( defined( 'AIOSEO_VERSION' ) ) {
		$general = json_decode( (string) get_option( 'aioseo_options' ), true )[ 'sitemap' ][ 'general' ] ?? [];
		$video   = json_decode( (string) get_option( 'aioseo_options_pro' ), true )[ 'sitemap' ][ 'video' ] ?? [];
		if ( ! empty( $general[ 'enable' ] ) && ! empty( $video[ 'enable' ] ) ) {
			return 'All in One SEO';
		}
	}

	//SEOPress: todo el sitemap cuelga de una única opción con los ajustes anidados
	if ( defined( 'SEOPRESS_VERSION' ) ) {
		$interruptores = (array) get_option( 'seopress_toggle', [] );
		$sitemap       = (array) get_option( 'seopress_xml_sitemap_option_name', [] );
		if ( ! empty( $interruptores[ 'toggle-xml-sitemap' ] ) && ! empty( $sitemap[ 'seopress_xml_sitemap_general_enable' ] ) && ! empty( $sitemap[ 'seopress_xml_sitemap_video_enable' ] ) ) {
			return 'SEOPress';
		}
	}

	return (string) apply_filters( 'apg_video_sitemap_seo_detectado', '' );
}

/**
 * Guarda que el aviso ya se ha descartado.
 *
 * @return void
 */
function apg_video_sitemap_descarta_aviso() {
	if ( ! isset( $_GET[ 'apg_video_sitemap_descarta' ] ) ) {
		return;
	}

	if ( ! isset( $_GET[ '_wpnonce' ] )
		|| ! wp_verify_nonce(
			sanitize_text_field( wp_unslash( $_GET[ '_wpnonce' ] ) ),
			'apg_video_sitemap_descarta_aviso'
		)
	) {
		return;
	}

	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}

	update_user_meta( get_current_user_id(), 'apg_video_sitemap_aviso_descartado', '1' );

	wp_safe_redirect( remove_query_arg( [ 'apg_video_sitemap_descarta', '_wpnonce' ] ) );
	exit;
}
add_action( 'admin_init', 'apg_video_sitemap_descarta_aviso' );

/**
 * Avisa de que otro plugin ya publica un sitemap de vídeos.
 *
 * @return void
 */
function apg_video_sitemap_aviso_seo() {
	$pantalla   = get_current_screen();
	//Se muestra sólo donde se puede actuar, que es la propia pantalla de plugins
	if ( ! $pantalla || 0 !== strpos( $pantalla->id, 'plugins' ) ) {
		return;
	}

	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}

	if ( get_user_meta( get_current_user_id(), 'apg_video_sitemap_aviso_descartado', true ) ) {
		return;
	}

	$seo        = apg_video_sitemap_detecta_seo();
	if ( '' === $seo ) {
		return;
	}

	$desactivar = wp_nonce_url(
		self_admin_url( 'plugins.php?action=deactivate&plugin=' . rawurlencode( APG_VIDEO_SITEMAP_DIRECCION ) ),
		'deactivate-plugin_' . APG_VIDEO_SITEMAP_DIRECCION
	);
	$descartar  = wp_nonce_url(
		add_query_arg( 'apg_video_sitemap_descarta', '1' ),
		'apg_video_sitemap_descarta_aviso'
	);
	?>
	<div class="notice notice-warning">
		<p>
			<strong>APG Google Video Sitemap Feed</strong>:
			<?php
			printf(
				/* translators: %s: name of the detected SEO plugin. */
				esc_html__( '%s is already publishing a video sitemap, so this plugin is probably redundant. Keeping both is harmless, but you only need one.', 'google-video-sitemap-feed-with-multisite-support' ),
				'<strong>' . esc_html( $seo ) . '</strong>'
			);
			?>
		</p>
		<p>
			<a href="<?php echo esc_url( $desactivar ); ?>" class="button button-secondary"><?php esc_html_e( 'Deactivate this plugin', 'google-video-sitemap-feed-with-multisite-support' ); ?></a>
			<a href="<?php echo esc_url( $descartar ); ?>" class="button button-link"><?php esc_html_e( 'Keep it and hide this notice', 'google-video-sitemap-feed-with-multisite-support' ); ?></a>
		</p>
	</div>
	<?php
}
add_action( 'admin_notices', 'apg_video_sitemap_aviso_seo' );
