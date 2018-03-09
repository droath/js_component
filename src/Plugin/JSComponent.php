<?php

namespace Drupal\js_component\Plugin;

use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Extension\ThemeHandlerInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Plugin\PluginBase;
use Drupal\Core\TypedData\TypedDataInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Define JS component plugin.
 */
class JSComponent extends PluginBase implements JSComponentInterface, ContainerFactoryPluginInterface {

  /**
   * @var ThemeHandlerInterface
   */
  protected $themeHandler;

  /**
   * @var ModuleHandlerInterface
   */
  protected $moduleHandler;

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
    $plugin_definition,
    ThemeHandlerInterface $theme_handler,
    ModuleHandlerInterface $module_handler) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->themeHandler = $theme_handler;
    $this->moduleHandler = $module_handler;
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
   * JS component root identifier.
   *
   * @return string
   */
  public function rootId() {
    return $this->getProperty('root_id') ?: 'root';
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
   * JS component has libraries.
   *
   * @return bool
   */
  public function hasLibraries() {
    return !empty($this->libraries());
  }

  /**
   * JS component template.
   *
   * @return string
   */
  public function template() {
    return $this->getProperty('template');
  }

  /**
   * JS component has template.
   *
   * @return bool
   */
  public function hasTemplate() {
    return !empty($this->template());
  }

  /**
   * JS component template path.
   *
   * @return string
   * @throws \Exception
   */
  public function getTemplatePath() {
    return "{$this->getProviderPath()}/{$this->templateFileInfo()['dirname']}";
  }

  /**
   * JS component template name, without extension.
   *
   * @return string
   */
  public function getTemplateName() {
    $file_info = $this->templateFileInfo();
    return basename($file_info['filename'], '.html');
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
   * @throws \Exception
   */
  public function processLibraries() {
    $libraries = $this->libraries();
    $asset_path = $this->getProviderPath();

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
   * Get JS component provider path.
   *
   * @return string
   * @throws \Exception
   */
  public function getProviderPath() {
    return drupal_get_path($this->getProviderType(), $this->provider());
  }

  /**
   * Get JS component template file info.
   *
   * @return array
   */
  protected function templateFileInfo() {
    return pathinfo($this->template());
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

  /**
   * Get Js component provider type.
   *
   * @return string
   * @throws \Exception
   */
  protected function getProviderType() {
    $provider = $this->provider();

    if ($this->themeHandler->themeExists($provider)) {
      return 'theme';
    }

    if ($this->moduleHandler->moduleExists($provider)) {
      return 'module';
    }

    throw new \Exception('JS component provider type is unknown.');
  }
}
