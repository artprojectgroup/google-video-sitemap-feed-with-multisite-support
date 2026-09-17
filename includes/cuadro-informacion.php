<?php
//Igual no deberías poder abrirme
defined( 'ABSPATH' ) || exit;

//Los textos se preparan aquí para que cada marcador lleve su comentario de traducción
$apg_video_sitemap_textos = apg_video_sitemap_dame_textos();
/* translators: %s: name of the plugin. */
$apg_video_sitemap_puntua = sprintf( __( 'Please, rate %s:', 'google-video-sitemap-feed-with-multisite-support' ), $apg_video_sitemap[ 'plugin' ] );
?>
<div class="informacion">
  <div class="fila">
    <div class="columna">
      <p>
        <?php esc_html_e( 'If you enjoy this plugin and find it helpful, please make a donation:', 'google-video-sitemap-feed-with-multisite-support' ); ?>
      </p>
      <p><a href="<?php echo esc_url( $apg_video_sitemap[ 'donacion' ] ); ?>" target="_blank" title="<?php echo esc_attr( $apg_video_sitemap_textos[ 'donacion' ] ); ?>"><span class="genericon genericon-cart"></span></a></p>
    </div>
    <div class="columna">
      <p><?php echo esc_html( 'Art Project Group:' ); ?></p>
      <p><a href="<?php echo esc_url( 'https://www.artprojectgroup.es' ); ?>" title="<?php echo esc_attr( 'Art Project Group' ); ?>" target="_blank"><strong class="artprojectgroup"><?php echo esc_html( 'APG' ); ?></strong></a></p>
    </div>
  </div>
  <div class="fila">
    <div class="columna">
      <p>
        <?php esc_html_e( 'Follow us:', 'google-video-sitemap-feed-with-multisite-support' ); ?>
      </p>
      <p><a href="<?php echo esc_url( 'https://www.facebook.com/artprojectgroup' ); ?>" title="<?php echo esc_attr( $apg_video_sitemap_textos[ 'facebook' ] ); ?>" target="_blank"><span class="genericon genericon-facebook-alt"></span></a> <a href="<?php echo esc_url( 'https://twitter.com/artprojectgroup' ); ?>" title="<?php echo esc_attr( $apg_video_sitemap_textos[ 'twitter' ] ); ?>" target="_blank"><span class="genericon genericon-twitter"></span></a> <a href="<?php echo esc_url( 'https://es.linkedin.com/in/artprojectgroup' ); ?>" title="<?php echo esc_attr( $apg_video_sitemap_textos[ 'linkedin' ] ); ?>" target="_blank"><span class="genericon genericon-linkedin"></span></a></p>
    </div>
    <div class="columna">
      <p>
        <?php esc_html_e( 'More plugins:', 'google-video-sitemap-feed-with-multisite-support' ); ?>
      </p>
      <p><a href="<?php echo esc_url( 'https://profiles.wordpress.org/artprojectgroup/' ); ?>" title="<?php echo esc_attr( $apg_video_sitemap_textos[ 'plugins' ] ); ?>" target="_blank"><span class="genericon genericon-wordpress"></span></a></p>
    </div>
  </div>
  <div class="fila">
    <div class="columna">
      <p>
        <?php esc_html_e( 'Contact us:', 'google-video-sitemap-feed-with-multisite-support' ); ?>
      </p>
      <p><a href="<?php echo esc_url( 'mailto:info@artprojectgroup.es' ); ?>" title="<?php echo esc_attr( $apg_video_sitemap_textos[ 'correo' ] ); ?>"><span class="genericon genericon-mail"></span></a> <a href="<?php echo esc_url( 'skype:artprojectgroup' ); ?>" title="<?php echo esc_attr( $apg_video_sitemap_textos[ 'skype' ] ); ?>"><span class="genericon genericon-skype"></span></a></p>
    </div>
    <div class="columna">
      <p>
        <?php esc_html_e( 'Documentation and Support:', 'google-video-sitemap-feed-with-multisite-support' ); ?>
      </p>
      <p><a href="<?php echo esc_url( $apg_video_sitemap[ 'plugin_url' ] ); ?>" title="<?php echo esc_attr( $apg_video_sitemap[ 'plugin' ] ); ?>"><span class="genericon genericon-book"></span></a> <a href="<?php echo esc_url( $apg_video_sitemap[ 'soporte' ] ); ?>" title="<?php esc_attr_e( 'Support', 'google-video-sitemap-feed-with-multisite-support' ); ?>"><span class="genericon genericon-cog"></span></a></p>
    </div>
  </div>
  <div class="fila final">
    <div class="columna">
        <p> <?php echo esc_html( $apg_video_sitemap_puntua ); ?> </p>
        <?php echo wp_kses_post( apg_video_sitemap_plugin( $apg_video_sitemap[ 'plugin_uri' ] ) ); ?> </div>
    <div class="columna final"></div>
  </div>
</div>
