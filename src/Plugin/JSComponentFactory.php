<?php

namespace Drupal\js_component\Plugin;

use Drupal\Component\Plugin\Factory\DefaultFactory;
use Drupal\Core\TypedData\TypedDataInterface;

/**
 * Define JS component plugin factory.
 */
class JSComponentFactory extends DefaultFactory {

  /**
   * {@inheritdoc}
   */
  public function createInstance($plugin_id, array $configuration = []) {
    $plugin_definition = $this->discovery->getDefinition($plugin_id);
    $data_definition = $this
      ->typedDataManager()
      ->createDataDefinition('js_component');

    $typed_data = $this
      ->typedDataManager()
      ->create($data_definition, $plugin_definition);

    // Validate that the plugin definition matches its requirements.
    foreach ($typed_data->validate() as $violation) {
      throw new \InvalidArgumentException(
        $violation->__toString()
      );
    }
    $configuration['typed_data'] = $typed_data;

    $plugin_class = static::getPluginClass($plugin_id, $plugin_definition, $this->interface);

    // If the plugin provides a factory method, pass the container to it.
    if (is_subclass_of($plugin_class, 'Drupal\Core\Plugin\ContainerFactoryPluginInterface')) {
      return $plugin_class::create(\Drupal::getContainer(), $configuration, $plugin_id, $plugin_definition);
    }

    // Otherwise, create the plugin directly.
    return new $plugin_class($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * @return TypedDataInterface
   */
  protected function typedDataManager() {
    return \Drupal::service('typed_data_manager');
  }
}
