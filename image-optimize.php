<?php

/*
Plugin Name: Image Optimization API
Plugin URI: http://bluetent.com
Description: Provides image optimization capabilities as users upload images to the library.
Author: ethan@bluetent.com
Version: 0.1
Author URI: http://bluetent.com/
Text Domain: image-optimize
License: GPLv2
*/

class ImageOptimize {

  protected $settings_instance;

  /**
   * Static storage for handler instances.
   * @var array
   */
  public static $handlers = array();

  /**
   * First require the base abstract class.
   */
  public function __construct() {
    include(sprintf('%s/classes/image-optimize-abstract.php', dirname(__FILE__)));
    add_action('init', array($this, 'register_optimization_handlers'), 0);
    if (is_admin()) {
      include(sprintf('%s/admin/settings.php', dirname(__FILE__)));
      if (class_exists('ImageOptimizeSettings')) {
        $this->settings_instance = new ImageOptimizeSettings();
      }
    }
    add_filter('image_optimize_register_handlers', array(
      $this,
      'register_jpg'
    ));
    add_filter('image_optimize_register_handlers', array(
      $this,
      'register_png'
    ));
  }

  /**
   * Registers ImageOptimizeAbstractHandler instances.
   * @param bool $reset
   */
  public function register_optimization_handlers($reset = FALSE) {
    if (empty(self::$handlers) && !$reset) {
      $handler_instances = array();
      $handler_definitions = apply_filters('image_optimize_register_handlers', array());
      foreach($handler_definitions as $class => $path) {
        if(file_exists($path)) {
          include($path);
          if(class_exists($class)) {
            $instance = new $class;
            $instance->register();
            $handler_instances[$class] = $instance;
          }
        }
      }
      self::set_handlers($handler_instances);
    }
  }

  /**
   * Returns a list of handlers.
   * @return array
   */
  public static function get_handlers() {
    return self::$handlers;
  }

  /**
   * Returns a list of handlers.
   * @return array
   */
  public static function set_handlers($handlers) {
    self::$handlers = $handlers;
  }

  /**
   * Registers a handler for JPG. This is a good example of how to register handlers.
   * @see $this->__construct().
   * @param $handlers
   * @return mixed
   */
  public function register_jpg($handlers) {
    $handlers['ImageOptimizeJpgHandler'] = sprintf('%s/classes/image-optimize-jpg.php', dirname(__FILE__));
    return $handlers;
  }

  /**
   * Registers a handler for PNG. This is a good example of how to register handlers.
   * @see $this->__construct().
   * @param $handlers
   * @return mixed
   */
  public function register_png($handlers) {
    $handlers['ImageOptimizePngHandler'] = sprintf('%s/classes/image-optimize-png.php', dirname(__FILE__));
    return $handlers;
  }

}

$ImageOptimize = new ImageOptimize();