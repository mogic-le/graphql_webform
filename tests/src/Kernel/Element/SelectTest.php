<?php

declare(strict_types = 1);

namespace Drupal\Tests\graphql_webform\Kernel\Element;

use Drupal\Tests\graphql_webform\Kernel\GraphQLWebformKernelTestBase;

/**
 * Tests for the WebformElementSelect type.
 *
 * @group graphql_webform
 */
class SelectTest extends GraphQLWebformKernelTestBase {

  /**
   * Tests select dropdowns.
   */
  public function testSelect(): void {
    $query = $this->getQueryFromFile('select.gql');
    $this->assertResults($query, ['webform_id' => 'graphql_webform_test_form'], [
      'form' => [
        'title' => 'GraphQL Webform test form',
        'elements' => [
          8 => [
            '__typename' => 'WebformElementSelect',
            'id' => 'select',
            'title' => 'Select',
            'description' => 'Choose wisely.',
            'options' => [
              0 => [
                'title' => 'Option 1',
                'value' => '1',
                '__typename' => 'WebformElementOption',
              ],
              1 => [
                'title' => 'Option 2',
                'value' => '2',
                '__typename' => 'WebformElementOption',
              ],
            ],
          ],
        ],
      ],
    ], $this->defaultCacheMetaData());
  }

}
