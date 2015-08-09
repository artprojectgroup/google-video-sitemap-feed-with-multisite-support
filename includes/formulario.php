<?php global $xml_video_sitemap; ?>

<div class="wrap">
  <h2>
    <?php _e( 'Google Video Sitemap Feed Options.', 'xml_video_sitemap' ); ?>
  </h2>
  <?php
		if ( $actualizacion ) {
			echo '<div id="message" class="updated fade"><p><strong>' . __( 'Options saved.', 'xml_video_sitemap' ) . '</strong></p></div>' . PHP_EOL;
		}
		$tab = 1;
		$configuracion = get_option( 'xml_video_sitemap' );
  ?>
  <h3><a href="<?php echo $xml_video_sitemap['plugin_url']; ?>" title="Art Project Group"><?php echo $xml_video_sitemap['plugin']; ?></a> </h3>
  <p>
    <?php _e( 'Dynamically generates a Google Video Sitemap and automatically submit updates to Google and Bing.', 'xml_video_sitemap' ); ?>
  </p>
  <?php include( 'cuadro-informacion.php' ); ?>
  <form method="post" action="">
    <div class="cabecera"> <a href="<?php echo $xml_video_sitemap['plugin_url']; ?>" title="<?php echo $xml_video_sitemap['plugin']; ?>" target="_blank"><img src="<?php echo plugins_url( '../assets/images/cabecera.jpg', __FILE__ ); ?>" class="imagen" alt="<?php echo $xml_video_sitemap['plugin']; ?>" /></a> </div>
    <table class="form-table apg-table">
      <tbody>
        <tr valign="top">
          <th scope="row"><?php _e( 'email:', 'xml_video_sitemap' ); ?>
          </th>
          <td><input id="correo" name="correo" type="checkbox" value="1" <?php echo ( isset( $configuracion['correo'] ) && $configuracion['correo'] == "1" ? "checked":  "" ); ?> tabindex="<?php echo $tab++; ?>" />
            <label for="correo">
              <?php _e( 'Send video error notifications by email.', 'xml_video_sitemap' ); ?>
            </label></td>
        </tr>
      </tbody>
    </table>
    <p class="submit">
      <input class="button-primary" type="submit" value="<?php _e( 'Save Changes', 'xml_video_sitemap' ); ?>"  name="submit" id="submit" tabindex="<?php echo $tab++; ?>" />
    </p>
  </form>
</div>