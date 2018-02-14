<?php

namespace Drupal\js_component\Plugin;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Plugin\PluginBase;
use Drupal\Core\TypedData\TypedDataInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Define JS component plugin.
 */
class JSComponent extends PluginBase implements JSComponentInterface, ContainerFactoryPluginInterface {

  /**
   * JS component constructor.
   *
   * @param array $configuration
   * @param string $plugin_id
   * @param mixed $plugin_definition
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
  }

  /**
   * {@inheritdoc}
   */
  public static function create(
    ContainerInterface $container,
    array $configuration,
    $plugin_id,
    $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('theme_handler'),
      $container->get('module_handler')
    );
  }

  /**
   * JS component label.
   *
   * @return string
   */
  public function label() {
    return $this->getProperty('label');
  }

  /**
   * JS component settings.
   *
   * @return array
   */
  public function settings() {
    return $this->getProperty('settings');
  }

  /**
   * JS component libraries.
   *
   * @return array
   */
  public function libraries() {
    return $this->getProperty('libraries');
  }

  /**
   * JS component provider.
   *
   * @return string
   */
  public function provider() {
    return $this->getProperty('provider');
  }

  /**
   * JS component identifier.
   *
   * @return string
   */
  public function componentId() {
    return $this->provider() . '.' . $this->getPluginId();
  }

  /**
   * Typed data validate.
   */
  public function validate() {
    return $this->typedData()->validate();
  }

  /**
   * Process JS component libraries.
   *
   * @return array
   */
  public function processLibraries() {
    $libraries = $this->libraries();
    $asset_path = drupal_get_path('theme', $this->provider());

    if (isset($libraries['js'])) {
      foreach ($libraries['js'] as $js_path => $js_info) {
        if (isset($js_info['type']) && $js_info['type'] === 'external') {
          continue;
        }
        unset($libraries['js'][$js_path]);
        $libraries['js']["/{$asset_path}{$js_path}"] = $js_info;
      }
    }

    if (isset($libraries['css'])) {
      foreach ($libraries['css'] as $type => $files) {
        foreach ($files as $css_path => $css_info) {
          unset($libraries['css'][$type][$css_path]);
          $libraries['css'][$type]["/{$asset_path}{$css_path}"] = $css_info;
        }
      }
    }

    return $libraries;
  }

  /**
   * Get typed data property value.
   *
   * @param $name
   *   The name of the property.
   *
   * @return mixed
   */
  protected function getProperty($name) {
    return $this->typedData()->get($name)->getValue();
  }

  /**
   * Typed data object.
   *
   * @return TypedDataInterface
   */
  protected function typedData() {
    return $this->configuration['typed_data'];
  }

}
