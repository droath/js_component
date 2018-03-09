<?php

namespace Drupal\js_component;

/**
 * Define JS component manager interface.
 */
interface JSComponentManagerInterface {

  /**
   * Get plugin definition instances.
   *
   * @return array
   */
  public function getDefinitionInstances();
}
