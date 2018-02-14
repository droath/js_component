<?php

namespace Drupal\js_component\Controller;

use Drupal\Core\Controller\ControllerBase;

class Page extends ControllerBase {



  public function build() {

    $service = \Drupal::service('plugin.manager.js_component');
    $service->useCaches(FALSE);
    $instance = $service->createInstance('popular_keywords');
    dpm($instance->processLibraries());




//    $service = \Drupal::service('plugin.manager.block');
//    $service->useCaches(FALSE);
//    dpm($service->getDefinitions());

//    $test = js_component_library_info_build();
//
//    dpm($test);

    return [];

  }

}
