<?php

declare(strict_types = 1);

namespace Drupal\Tests\graphql_webform\Kernel\Element;

use Drupal\Tests\graphql_webform\Kernel\GraphQLWebformKernelTestBase;

/**
 * Tests for the WebformElementMarkup type.
 *
 * @group graphql_webform
 */
class MarkupTest extends GraphQLWebformKernelTestBase {

  /**
   * Tests custom markup.
   */
  public function testMarkup(): void {
    $query = $this->getQueryFromFile('markup.gql');
    $this->assertResults($query, ['webform_id' => 'graphql_webform_test_form'], [
      'form' => [
        'title' => 'GraphQL Webform test form',
        'elements' => [
          5 => [
            '__typename' => 'WebformElementMarkup',
            'id' => 'markup',
            'markup' => '<strong>Markup</strong>',
          ],
        ],
      ],
    ], $this->defaultCacheMetaData());
  }

}
