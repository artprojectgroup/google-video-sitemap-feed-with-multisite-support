<?php
//Igual no deberías poder abrirme
defined( 'ABSPATH' ) || exit;

global $apg_video_sitemap; 
$apg_video_sitemap_tab            = 1;
$apg_video_sitemap_configuracion  = get_option( 'xml_video_sitemap' );
if ( ! is_array( $apg_video_sitemap_configuracion ) ) {
	$apg_video_sitemap_configuracion = [];
}
?>
<div class="wrap">
	<h2>
		<?php esc_html_e( 'Google Video Sitemap Feed Options.', 'google-video-sitemap-feed-with-multisite-support' ); ?>
	</h2>
	<h3><a href="<?php echo esc_url( $apg_video_sitemap[ 'plugin_url' ] ); ?>" title="<?php echo esc_attr( 'Art Project Group' ); ?>"><?php echo esc_html( $apg_video_sitemap[ 'plugin' ] ); ?></a> </h3>
	<p>
		<?php esc_html_e( 'Dynamically generates a Google Video Sitemap.', 'google-video-sitemap-feed-with-multisite-support' ); ?>
	</p>
	<?php include( plugin_dir_path( __FILE__ ) . 'cuadro-informacion.php' ); ?>
	<form method="post" action="options.php">
		<?php settings_fields( 'apg_video_sitemap_settings_group' ); ?>
		<div class="cabecera"> <a href="<?php echo esc_url( $apg_video_sitemap[ 'plugin_url' ] ); ?>" title="<?php echo esc_attr( $apg_video_sitemap[ 'plugin' ] ); ?>" target="_blank"><img src="<?php echo esc_url( plugins_url( 'assets/images/cabecera.jpg', APG_VIDEO_SITEMAP_DIRECCION ) ); ?>" class="imagen" alt="<?php echo esc_attr( $apg_video_sitemap[ 'plugin' ] ); ?>" /></a> </div>
		<table class="form-table apg-table">
			<tbody>
				<tr valign="top">
					<th scope="row"><?php esc_html_e( 'email:', 'google-video-sitemap-feed-with-multisite-support' ); ?>
					</th>
					<td><input id="xml_video_sitemap_correo" name="xml_video_sitemap[correo]" type="checkbox" value="1" <?php checked( ! empty( $apg_video_sitemap_configuracion[ 'correo' ] ) ); ?> tabindex="<?php echo esc_attr( $apg_video_sitemap_tab++ ); ?>" />
						<label for="xml_video_sitemap_correo">
							<?php esc_html_e( 'Send video error notifications by email.', 'google-video-sitemap-feed-with-multisite-support' ); ?>
						</label></td>
				</tr>
			</tbody>
		</table>
		<p class="submit">
		<input class="button-primary" type="submit" value="<?php esc_attr_e( 'Save Changes', 'google-video-sitemap-feed-with-multisite-support' ); ?>"  name="submit" id="submit" tabindex="<?php echo esc_attr( $apg_video_sitemap_tab++ ); ?>" />
		</p>
	</form>
</div>
