<?php

declare(strict_types = 1);

namespace Drupal\Tests\graphql_webform\Kernel\Element;

use Drupal\Tests\graphql_webform\Kernel\GraphQLWebformKernelTestBase;

/**
 * Tests for the WebformElementActions type.
 *
 * @group graphql_webform
 */
class ActionsTest extends GraphQLWebformKernelTestBase {

  /**
   * Tests form submission actions.
   */
  public function testActions(): void {
    $query = $this->getQueryFromFile('actions.gql');
    $this->assertResults($query, ['webform_id' => 'graphql_webform_test_form'], [
      'form' => [
        'title' => 'GraphQL Webform test form',
        'elements' => [
          9 => [
            '__typename' => 'WebformElementActions',
            'submitLabel' => 'Set sail for adventure',
          ],
        ],
      ],
    ], $this->defaultCacheMetaData());
  }

}
