<?php

namespace Drupal\graphql_webform\Plugin\GraphQL\Fields;

use Drupal\graphql\GraphQL\Execution\ResolveContext;
use Drupal\graphql\Plugin\GraphQL\Fields\FieldPluginBase;
use Drupal\webform\Entity\Webform;
use GraphQL\Type\Definition\ResolveInfo;

/**
 * A query field that finds a Webform by its ID.
 *
 * @GraphQLField(
 *   id = "webform_by_id",
 *   type = "Webform",
 *   name = "webformById",
 *   nullable = true,
 *   multi = false,
 *   arguments = {
 *     "webform_id" = "String!",
 *   },
 * )
 */
class WebformQueryById extends FieldPluginBase {

  /**
   * {@inheritdoc}
   */
  public function resolveValues($value, array $args, ResolveContext $context, ResolveInfo $info) {

    // Load the webform.
    $webform = Webform::load($args['webform_id']);

    if ($webform) {
      yield $webform;
    }

    yield NULL;

  }

}
