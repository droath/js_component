<?php

namespace Drupal\js_component;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Extension\ThemeHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\Core\Plugin\Discovery\YamlDiscoveryDecorator;
use Drupal\Core\TypedData\TypedDataManagerInterface;
use Drupal\js_component\Plugin\JSComponent;
use Drupal\js_component\Plugin\JSComponentFactory;

/**
 * Define JS component manager.
 */
class JSComponentManager extends DefaultPluginManager implements JSComponentManagerInterface{

  /**
   * @var ThemeHandlerInterface
   */
  protected $themeHandler;

  /**
   * @var TypedDataManagerInterface
   */
  protected $typedDataManager;

  /**
   * @var array
   */
  protected $defaults = [
    'class' => JSComponent::class
  ];

  /**
   * JS component manager constructor.
   *
   * @param $namespaces
   * @param ModuleHandlerInterface $module_handler
   * @param ThemeHandlerInterface $theme_handler
   * @param CacheBackendInterface $cache_backend
   */
  public function __construct(
    $namespaces,
    ModuleHandlerInterface $module_handler,
    ThemeHandlerInterface $theme_handler,
    CacheBackendInterface $cache_backend,
    TypedDataManagerInterface $typed_data_manager) {
    parent::__construct(
      'Plugin/JSComponent',
      $namespaces,
      $module_handler,
      '\Drupal\js_component\Plugin\JSComponentInterface',
      '\Drupal\js_component\Annotation\JSComponent'
    );
    $this->themeHandler = $theme_handler;
    $this->alterInfo(['js_component_info']);
    $this->setCacheBackend($cache_backend, 'js_component');
    $this->typedDataManager = $typed_data_manager;
    $this->factory = new JSComponentFactory($this, $this->pluginInterface);
  }

  /**
   * {@inheritdoc}
   */
  public function getDiscovery() {
    $discovery = parent::getDiscovery();

    $directories = array_merge(
      $this->themeHandler->getThemeDirectories(),
      $this->moduleHandler->getModuleDirectories()
    );

    $this->discovery = new YamlDiscoveryDecorator(
      $discovery, 'js_component', $directories
    );

    return $this->discovery;
  }

  /**
   * {@inheritdoc}
   */
  public function getDefinitionInstances() {
    $instances = [];

    foreach (array_keys($this->getDefinitions()) as $plugin_id) {
      $instances[$plugin_id] = $this->createInstance($plugin_id);
    }

    return $instances;
  }

  /**
   * {@inheritdoc}
   */
  protected function providerExists($provider) {
    return parent::providerExists($provider) ||
      $this->themeHandler->themeExists($provider);
  }
}
