<?php

namespace Drupal\graphql_webform\Plugin\GraphQL\Fields\Element;

use Drupal\graphql\GraphQL\Execution\ResolveContext;
use Drupal\graphql\Plugin\GraphQL\Fields\FieldPluginBase;
use GraphQL\Type\Definition\ResolveInfo;

/**
 * Retrieve the pattern error property from a TextBase form element.
 *
 * @GraphQLField(
 *   secure = true,
 *   parents = {"WebformElementTextBase"},
 *   id = "webform_element_pattern",
 *   name = "pattern",
 *   type = "WebformElementValidationPattern",
 * )
 */
class WebformElementPattern extends FieldPluginBase {

  /**
   * {@inheritdoc}
   */
  public function resolveValues($value, array $args, ResolveContext $context, ResolveInfo $info) {
    if (isset($value['#pattern'])) {
      $response['value'] = TRUE;
      $response['message'] = $value['#pattern_error'] ?? '';
      $response['rule'] = $value['#pattern'];
      $response['type'] = 'WebformElementValidationPattern';
      yield $response;
    }
  }

}
