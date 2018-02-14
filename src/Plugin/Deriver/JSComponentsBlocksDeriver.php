<?php

namespace Drupal\js_component\Plugin\Deriver;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\Component\Plugin\PluginManagerInterface;
use Drupal\Core\Plugin\Discovery\ContainerDeriverInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Define JS component blocks deriver.
 */
class JSComponentsBlocksDeriver extends DeriverBase implements ContainerDeriverInterface {

  /**
   * @var PluginManagerInterface
   */
  protected $jsComponentManager;

  /**
   * JS components blocks constructor.
   *
   * @param PluginManagerInterface $js_component_manager
   */
  function __construct(PluginManagerInterface $js_component_manager) {
    $this->jsComponentManager = $js_component_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, $base_plugin_id) {
    return new static(
      $container->get('plugin.manager.js_component')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    $instances = $this->jsComponentManager->getDefinitionInstances();

    foreach ($instances as $plugin_id => $instance) {
      $this->derivatives[$plugin_id] = $base_plugin_definition;
      $this->derivatives[$plugin_id]['admin_label'] = $instance->label();
      // @todo It feels a bit hacky passing arbitrary data that hasn't been
      // defined in the annotation. Alternatives welcomed?
      $this->derivatives[$plugin_id]['settings'] = $instance->settings();
      $this->derivatives[$plugin_id]['component_id'] = $instance->componentId();
      $this->derivatives[$plugin_id]['config_dependencies']['config'] = [];
    }

    return $this->derivatives;
  }
}
