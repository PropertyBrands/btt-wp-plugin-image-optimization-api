<?php
/**
 * Template file for laying out the settings page for Image Optimize.
 */
?>
<form action="options.php" method='post'>

  <h2>Image Optimization API Settings</h2>

  <?php
  settings_fields('image_optimize');
  do_settings_sections('image-optimize');
  submit_button();

  ?>

</form>