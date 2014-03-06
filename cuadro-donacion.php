<?php $plugin = xml_video_sitemap_plugin($xml_video_sitemap['plugin_uri']); ?>
<div class="donacion">
  <p>
    <?php _e('If you enjoyed and find helpful this plugin, please make a donation:', 'xml_video_sitemap'); ?>
  </p>
  <p><a href="<?php echo $xml_video_sitemap['donacion']; ?>" target="_blank" title="<?php _e('Make a donation by ', 'xml_video_sitemap'); ?>APG"><span class="icon-bills"></span></a></p>
  <div>
    <p>Art Project Group:</p>
    <p><a href="http://www.artprojectgroup.es" title="Art Project Group" target="_blank"><strong class="artprojectgroup">APG</strong></a></p>
  </div>
  <div>
    <p>
      <?php _e('Follow us:', 'xml_video_sitemap'); ?>
    </p>
    <p><a href="https://www.facebook.com/artprojectgroup" title="<?php _e('Follow us on ', 'xml_video_sitemap'); ?>Facebook" target="_blank"><span class="icon-facebook6"></span></a> <a href="https://twitter.com/artprojectgroup" title="<?php _e('Follow us on ', 'xml_video_sitemap'); ?>Twitter" target="_blank"><span class="icon-social19"></span></a> <a href="https://plus.google.com/+ArtProjectGroupES" title="<?php _e('Follow us on ', 'xml_video_sitemap'); ?>Google+" target="_blank"><span class="icon-google16"></span></a> <a href="http://es.linkedin.com/in/artprojectgroup" title="<?php _e('Follow us on ', 'xml_video_sitemap'); ?>LinkedIn" target="_blank"><span class="icon-logo"></span></a></p>
  </div>
  <div>
    <p>
      <?php _e('More plugins:', 'xml_video_sitemap'); ?>
    </p>
    <p><a href="http://profiles.wordpress.org/artprojectgroup/" title="<?php _e('More plugins on ', 'xml_video_sitemap'); ?>WordPress" target="_blank"><span class="icon-wordpress2"></span></a></p>
  </div>
  <div>
    <p>
      <?php _e('Contact with us:', 'xml_video_sitemap'); ?>
    </p>
    <p><a href="mailto:info@artprojectgroup.es" title="<?php _e('Contact with us by ', 'xml_video_sitemap'); ?>e-mail"><span class="icon-open21"></span></a> <a href="skype:artprojectgroup" title="<?php _e('Contact with us by ', 'xml_video_sitemap'); ?>Skype"><span class="icon-social6"></span></a></p>
  </div>
  <div>
    <p>
      <?php _e('Documentation and Support:', 'xml_video_sitemap'); ?>
    </p>
    <p><a href="<?php echo $xml_video_sitemap['plugin_url']; ?>" title="<?php echo $xml_video_sitemap['plugin']; ?>"><span class="icon-work"></span></a></p>
  </div>
  <div>
    <p> <?php echo sprintf(__('Please, rate %s:', 'xml_video_sitemap'), $xml_video_sitemap['plugin']); ?> </p>
    <div class="star-holder rate">
      <div style="width: <?php echo esc_attr(str_replace(',', '.', $plugin['rating'])); ?>px;" class="star-rating"></div>
      <div class="star-rate"> <a title="<?php _e('***** Fantastic!', 'xml_video_sitemap'); ?>" href="<?php echo $xml_video_sitemap['puntuacion']; ?>?rate=5#postform"><span></span></a> <a title="<?php _e('**** Great', 'xml_video_sitemap'); ?>" href="<?php echo $xml_video_sitemap['puntuacion']; ?>?rate=4#postform"><span></span></a> <a title="<?php _e('*** Good', 'xml_video_sitemap'); ?>" href="<?php echo $xml_video_sitemap['puntuacion']; ?>?rate=3#postform"><span></span></a> <a title="<?php _e('** Works', 'xml_video_sitemap'); ?>" href="<?php echo $xml_video_sitemap['puntuacion']; ?>?rate=2#postform"><span></span></a> <a title="<?php _e('* Poor', 'xml_video_sitemap'); ?>" href="<?php echo $xml_video_sitemap['puntuacion']; ?>?rate=1#postform"><span></span></a> </div>
    </div>
  </div>
</div>