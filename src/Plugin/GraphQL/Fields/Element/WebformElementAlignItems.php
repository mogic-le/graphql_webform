<?php

declare(strict_types = 1);

namespace Drupal\graphql_webform\Plugin\GraphQL\Fields\Element;

use Drupal\graphql\GraphQL\Execution\ResolveContext;
use Drupal\graphql\Plugin\GraphQL\Fields\FieldPluginBase;
use GraphQL\Type\Definition\ResolveInfo;

/**
 * Retrieve the 'align items' property from a WebformFlexbox form element.
 *
 * @GraphQLField(
 *   secure = true,
 *   parents = {"WebformElementFlexbox"},
 *   id = "webform_element_align_items",
 *   name = "alignItems",
 *   type = "String",
 * )
 */
class WebformElementAlignItems extends FieldPluginBase {

  /**
   * {@inheritdoc}
   */
  public function resolveValues($value, array $args, ResolveContext $context, ResolveInfo $info) {
    if (!empty($value['#align_items'])) {
      yield (string) $value['#align_items'];
    }
    yield 'flex-start';
  }

}
