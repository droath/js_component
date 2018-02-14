<?php

namespace Drupal\js_component\Plugin\DataType;
use Drupal\Core\Annotation\Translation;
use Drupal\Core\TypedData\Annotation\DataType;
use Drupal\Core\TypedData\Plugin\DataType\Map;

/**
 * Define JS component data type.
 *
 * @DataType(
 *   id = "js_component",
 *   label = @Translation("JS Component"),
 *   definition_class = "\Drupal\js_component\TypedData\JSComponentDataDefinition"
 * )
 */
class JSComponentDataType extends Map {



}
