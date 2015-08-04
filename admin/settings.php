<?php
/**
 * ImageOptimize API Settings
 */
class ImageOptimizeSettings {
  private $handlers = array();
  public function __construct() {
    add_action( 'admin_menu', array($this, 'image_optimize_add_admin_menu'), 998);
    add_action( 'admin_init', array($this, 'image_optimize_settings_init'), 999);
  }

  /**
   * Attach settings from handlers.
   */
  public function image_optimize_settings_init() {
    $this->handlers = ImageOptimize::get_handlers();
    if(!empty($this->handlers)) {
      foreach($this->handlers as $handler) {
        $handler->register_settings();
      }
    }
  }

  /**
   * Menu Callback.
   */
  public function image_optimize_add_admin_menu() {
    add_options_page(
      'Image Optimize Settings',
      'Image Optimize ',
      'manage_options',
      'image-optimize',
      array($this, 'image_optimize_options_page')
    );
  }

  /**
   * Renders HTML.
   */
  public function image_optimize_options_page() {
    if(!current_user_can('manage_options')) {
      wp_die(__('You do not have sufficient permissions to access this page.'));
    }
    // Render the settings template
    include(sprintf("%s/templates/settings.php", dirname(__FILE__)));
  }
}