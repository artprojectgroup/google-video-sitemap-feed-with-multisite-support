<div class="informacion">
  <div class="fila">
    <div class="columna">
      <p>
        <?php _e( 'If you enjoyed and find helpful this plugin, please make a donation:', 'xml_video_sitemap' ); ?>
      </p>
      <p><a href="<?php echo $xml_video_sitemap['donacion']; ?>" target="_blank" title="<?php _e( 'Make a donation by ', 'xml_video_sitemap' ); ?>APG"><span class="genericon genericon-cart"></span></a></p>
    </div>
    <div class="columna">
      <p>Art Project Group:</p>
      <p><a href="http://www.artprojectgroup.es" title="Art Project Group" target="_blank"><strong class="artprojectgroup">APG</strong></a></p>
    </div>
  </div>
  <div class="fila">
    <div class="columna">
      <p>
        <?php _e( 'Follow us:', 'xml_video_sitemap' ); ?>
      </p>
      <p><a href="https://www.facebook.com/artprojectgroup" title="<?php _e( 'Follow us on ', 'xml_video_sitemap' ); ?>Facebook" target="_blank"><span class="genericon genericon-facebook-alt"></span></a> <a href="https://twitter.com/artprojectgroup" title="<?php _e( 'Follow us on ', 'xml_video_sitemap' ); ?>Twitter" target="_blank"><span class="genericon genericon-twitter"></span></a> <a href="https://plus.google.com/+ArtProjectGroupES" title="<?php _e( 'Follow us on ', 'xml_video_sitemap' ); ?>Google+" target="_blank"><span class="genericon genericon-googleplus-alt"></span></a> <a href="http://es.linkedin.com/in/artprojectgroup" title="<?php _e( 'Follow us on ', 'xml_video_sitemap' ); ?>LinkedIn" target="_blank"><span class="genericon genericon-linkedin"></span></a></p>
    </div>
    <div class="columna">
      <p>
        <?php _e( 'More plugins:', 'xml_video_sitemap' ); ?>
      </p>
      <p><a href="http://profiles.wordpress.org/artprojectgroup/" title="<?php _e( 'More plugins on ', 'xml_video_sitemap' ); ?>WordPress" target="_blank"><span class="genericon genericon-wordpress"></span></a></p>
    </div>
  </div>
  <div class="fila">
    <div class="columna">
      <p>
        <?php _e( 'Contact with us:', 'xml_video_sitemap' ); ?>
      </p>
      <p><a href="mailto:info@artprojectgroup.es" title="<?php _e( 'Contact with us by ', 'xml_video_sitemap' ); ?>e-mail"><span class="genericon genericon-mail"></span></a> <a href="skype:artprojectgroup" title="<?php _e( 'Contact with us by ', 'xml_video_sitemap' ); ?>Skype"><span class="genericon genericon-skype"></span></a></p>
    </div>
    <div class="columna">
      <p>
        <?php _e( 'Documentation and Support:', 'xml_video_sitemap' ); ?>
      </p>
      <p><a href="<?php echo $xml_video_sitemap['plugin_url']; ?>" title="<?php echo $xml_video_sitemap['plugin']; ?>"><span class="genericon genericon-book"></span></a> <a href="<?php echo $xml_video_sitemap['soporte']; ?>" title="<?php _e( 'Support', 'xml_video_sitemap' ); ?>"><span class="genericon genericon-cog"></span></a></p>
    </div>
  </div>
  <div class="fila final">
    <div class="columna">
      <p> <?php echo sprintf( __( 'Please, rate %s:', 'xml_video_sitemap' ), $xml_video_sitemap['plugin'] ); ?> </p>
      <?php echo xml_video_sitemap_plugin( $xml_video_sitemap['plugin_uri'] ); ?> </div>
    <div class="columna final"></div>
  </div>
</div>
