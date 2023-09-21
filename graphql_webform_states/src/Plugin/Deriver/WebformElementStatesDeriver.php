<?php

declare(strict_types = 1);

namespace Drupal\graphql_webform_states\Plugin\Deriver;

use Drupal\Component\Plugin\Derivative\DeriverBase;
use Drupal\graphql\Utility\StringHelper;

/**
 * Deriver for webform element states.
 */
class WebformElementStatesDeriver extends DeriverBase {

  // The possible states a webform element can be in.
  protected const STATES = [
    'visible',
    'invisible',
    'visible_slide',
    'invisible_slide',
    'enabled',
    'disabled',
    'readwrite',
    'readonly',
    'expanded',
    'collapsed',
    'required',
    'optional',
    'checked',
    'unchecked',
  ];

  /**
   * {@inheritdoc}
   */
  public function getDerivativeDefinitions($base_plugin_definition) {
    foreach (self::STATES as $state) {
      $derivative = [
        'id' => $state,
        'name' => StringHelper::propCase($state),
        'type' => 'WebformElementState',
        'parents' => ['WebformElementStates'],
      ] + $base_plugin_definition;
      $this->derivatives[$state] = $derivative;
    }
    return parent::getDerivativeDefinitions($base_plugin_definition);
  }

}
