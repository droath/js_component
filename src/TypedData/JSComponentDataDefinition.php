<?php

namespace Drupal\js_component\TypedData;

use Drupal\Core\StringTranslation\TranslatableMarkup;
use Drupal\Core\TypedData\ComplexDataDefinitionBase;
use Drupal\Core\TypedData\DataDefinition;
use Drupal\Core\TypedData\MapDataDefinition;

/**
 * Define JS component data definition.
 */
class JSComponentDataDefinition extends ComplexDataDefinitionBase {

  /**
   * {@inheritdoc}
   */
  public function getPropertyDefinitions() {
    $properties['provider'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Provider'))
      ->setRequired(TRUE);
    $properties['label'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Label'))
      ->setRequired(TRUE);
    $properties['description'] = DataDefinition::create('string')
      ->setLabel(new TranslatableMarkup('Description'));
    $properties['settings'] = MapDataDefinition::create()
      ->setLabel(new TranslatableMarkup('Settings'));
    $properties['libraries'] = MapDataDefinition::create()
      ->setLabel(new TranslatableMarkup('Libraries'));

    return $properties;
  }
}
