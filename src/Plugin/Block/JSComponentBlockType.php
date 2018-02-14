<?php

namespace Drupal\js_component\Plugin\Block;

use Drupal\Core\Annotation\Translation;
use Drupal\Core\Asset\LibraryDiscoveryInterface;
use Drupal\Core\Block\Annotation\Block;
use Drupal\Core\Block\BlockBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Render\Element\FormElementInterface;
use Drupal\Core\Render\ElementInfoManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Define JS component block.
 *
 * @Block(
 *   id = "js_component",
 *   category = @Translation("JS Component"),
 *   admin_label = @Translation("JS Component"),
 *   deriver = "\Drupal\js_component\Plugin\Deriver\JSComponentsBlocksDeriver"
 * )
 */
class JSComponentBlockType extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * @var LibraryDiscoveryInterface
   */
  protected $libraryDiscovery;

  /**
   * @var ElementInfoManagerInterface
   */
  protected $elementInfoManager;

  /**
   * {@inheritdoc}
   */
  public function defaultConfiguration() {
    return [
      'settings' => [],
    ] + parent::defaultConfiguration();
  }

  /**
   * JS component block constructor.
   *
   * @param array $configuration
   * @param $plugin_id
   * @param $plugin_definition
   * @param ElementInfoManagerInterface $element_info_manager
   */
  public function __construct(
    array $configuration,
    $plugin_id,
    $plugin_definition,
    LibraryDiscoveryInterface $library_discovery,
    ElementInfoManagerInterface $element_info_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->elementInfoManager = $element_info_manager;
    $this->libraryDiscovery = $library_discovery;
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
      $container->get('library.discovery'),
      $container->get('plugin.manager.element_info')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function blockForm($form, FormStateInterface $form_state) {
    $form = parent::blockForm($form, $form_state);
    $form['settings'] = $this->buildSettingsForm([], $form_state);
    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function blockSubmit($form, FormStateInterface $form_state) {
    parent::blockSubmit($form, $form_state);
    $this->configuration['settings'] = $form_state->getValue('settings');
  }

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = [
      '#markup' => '<div id="root"></div>',
    ];

    // Attach JS settings if they've been set.
    if ($settings = $this->getConfigurationSettings()) {
      $build['#attached']['drupalSettings']['js_component'] = [
        'settings' => $settings
      ];
    }

    // Attach library to component if it has been defined.
    if ($this->hasLibraryForComponent()) {
      $build['#attached']['library'][] = "js_component/{$this->getComponentId()}";
    }

    return $build;
  }

  /**
   * JS component identifier.
   *
   * @return mixed
   */
  protected function getComponentId() {
    return $this->pluginDefinition['component_id'];
  }

  /**
   * JS component has libraries defined.
   *
   * @return bool
   */
  protected function hasLibraryForComponent() {
    $status = $this
      ->libraryDiscovery
      ->getLibraryByName('js_component', "{$this->getComponentId()}");

    return $status !== FALSE ? TRUE : FALSE;
  }

  /**
   * Build JS component settings form.
   *
   * @param array $form
   * @param FormStateInterface $form_state
   *
   * @return array
   */
  protected function buildSettingsForm(array $form, FormStateInterface $form_state) {
    $definition = $this->getPluginDefinition();

    if (!isset($definition['settings'])) {
      return $form;
    }
    $settings = $this->getConfigurationSettings();

    foreach ($definition['settings'] as $field_name => $field_info) {
      if (!isset($field_info['type'])
        || !$this->elementIsValid($field_info['type'])) {
        continue;
      }
      $element = $this->formatFormElement($field_info);

      if (isset($settings[$field_name])
        && !empty($settings[$field_name])) {
        $element['#default_value'] = $settings[$field_name];
      }

      $form[$field_name] = $element;
    }

    return $form;
  }

  /**
   * Format form element.
   *
   * @param $element_info
   *
   * @return array
   */
  protected function formatFormElement($element_info) {
    $element = [];

    foreach ($element_info as $key => $value) {
      if (empty($value)) {
        continue;
      }
      $element["#{$key}"] = $value;
    }

    return $element;
  }

  /**
   * @param $type
   *
   * @return bool
   */
  protected function elementIsValid($type) {
    if (!$this->elementInfoManager->hasDefinition($type)) {
      return FALSE;
    }
    $element_type = $this
      ->elementInfoManager
      ->createInstance($type);

    return $element_type instanceof FormElementInterface;
  }

  /**
   * Get configuration settings.
   *
   * @return array
   */
  protected function getConfigurationSettings() {
    return $this->getConfiguration()['settings'];
  }

}
