<?php

namespace Drupal\graphql_webform\Plugin\GraphQL\Fields\Webform;

use Drupal\graphql\GraphQL\Execution\ResolveContext;
use Drupal\graphql\Plugin\GraphQL\Fields\FieldPluginBase;
use Drupal\webform\Utility\WebformYaml as UtilityWebformYaml;
use GraphQL\Type\Definition\ResolveInfo;

/**
 * A field that represents all the form elements in a Webform.
 *
 * @GraphQLField(
 *   secure = true,
 *   parents = {"Webform"},
 *   id = "webform_yaml",
 *   name = "yaml",
 *   type = "String",
 * )
 */
class WebformYaml extends FieldPluginBase {

  /**
   * {@inheritdoc}
   */
  public function resolveValues($value, array $args, ResolveContext $context, ResolveInfo $info) {

    /** @var \Drupal\webform\WebformInterface $webform */
    $definition = \Drupal::service('entity_type.manager')->getDefinition('webform');
    $config_name = $definition->getConfigPrefix() . '.' . $value->getConfigTarget();
    $data = \Drupal::config($config_name)->getRawData();
    yield UtilityWebformYaml::encode($data);

  }

}
