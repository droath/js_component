<?php

namespace Drupal\js_component\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Define JS component annotation.
 *
 * @Annotation
 */
class JSComponent extends Plugin {

  /**
   * @var
   */
  public $id;

  /**
   * @var
   */
  public $label;
}
