<?php
/**
 * Abstract Class ImageOptimize
 *
 * Classes that extend this one for the most part will only need to override
 * attachment_optimize to being working.
 */
abstract class ImageOptimizeAbstractHandler {
  public $binary_path;
  public $mime_type;
  public $handler_slug;

  public function __construct($handler_slug = NULL, $mime_type = NULL) {
    if(!empty($handler_slug) && !empty($mime_type)) {
      $this->handler_slug = $handler_slug;
      $this->mime_type = $mime_type;
      $this->binary_path = get_option('image_optimize_binary_' . $this->handler_slug);
    }
  }

  /**
   * Registers the optimization handler. Provides classes a way to setup upon
   * registration.
   * @return array $handlers
   */
  public function register() {}

  /**
   * Optimize images by attachment_id.
   * @param $id
   * @throws \Exception
   */
  public function attachment_optimize($id) {
    throw new Exception(__('Method must be overridden.', 'image-optimize'));
  }

  /**
   * Retrieves image paths from an attachment ID.
   * @param $id
   * @return array
   */
  public static function get_images($id) {
    $sizes = get_intermediate_image_sizes();
    $sizes[] = 'full';
    $retval = array();
    foreach($sizes as $size) {
      $img_src = wp_get_attachment_image_src($id, $size);
      $real_path = ABSPATH . ltrim(str_replace(get_bloginfo('url'), '', $img_src[0]), '/');
      $retval[] = $real_path;
    }
    return $retval;
  }

  /**
   * Settings Section Helper
   */
  public function settings_section() {
    echo __('Provides Optimization Settings for: ' . $this->mime_type);
  }

  /**
   * Render a simple text field.
   * @param $args
   */
  public function binary_path_field($args) {
    $field = $args['field'];
    $value = get_option($field);
    echo sprintf('<input type="text" name="%s" id="%s" value="%s" />', $field, $field, $value);
  }

  /**
   * Sanitizes our path.
   * @param $binary_path
   * @return string
   */
  public function binary_path_validate($binary_path) {
    $f = sanitize_text_field($binary_path);
    return $f;
  }

  /**
   * Registers settings with the Settings API.
   */
  public function register_settings() {
    register_setting('image_optimize', $this->handler_slug . '_binary', array($this, 'binary_path_validate'));
    add_settings_section(
      $this->handler_slug .'_section',
      __( 'Optimization Settings for ' . $this->mime_type, 'image-optimize' ),
      array($this, 'settings_section'),
      'image-optimize'
    );

    add_settings_field(
      $this->handler_slug . '_binary',
      __( 'Full Binary Path for ' . $this->mime_type, 'image-optimize' ),
      array($this, 'binary_path_field'),
      'image-optimize',
      $this->handler_slug .'_section',
      array('field' => $this->handler_slug . '_binary')
    );
  }
}