<?php

namespace Drupal\js_component\Annotation;

use Drupal\Core\Block\Annotation\Block;

/**
 * Define JS component block annotation.
 *
 * @Annotation
 */
class JSComponentBlock extends Block {

  /**
   * An array of field settings.
   *
   * @var array
   */
  public $settings = [];
}
