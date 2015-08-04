<?php
/**
 * Class ImageOptimizePng
 */
class ImageOptimizePngHandler extends ImageOptimizeAbstractHandler {
  public $mime_type = 'image/png';
  public $handler_slug = 'image_optimize_png';

  public function __construct() {
    parent::__construct($this->handler_slug, $this->mime_type);
  }

  public function attachment_optimize($id) {
    $images = self::get_images($id);
    $cmd = "cd " . escapeshellarg(ABSPATH) . "; ";
    if($images) {
      foreach($images as $img) {
        $cmd .= $this->binary_path . " " . escapeshellarg($img);
      }
    }
    if(!empty($cmd)) {
      shell_exec($cmd);
      //@todo: something with the output...
    }
  }
}