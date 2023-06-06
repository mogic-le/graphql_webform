<?php

declare(strict_types = 1);

namespace Drupal\Tests\graphql_webform\Kernel\Element;

use Drupal\Tests\graphql_webform\Kernel\GraphQLWebformKernelTestBase;

/**
 * Tests for the WebformElementRadios type.
 *
 * @group graphql_webform
 */
class RadiosTest extends GraphQLWebformKernelTestBase {

  /**
   * Tests radio buttons.
   */
  public function testRadios(): void {
    $query = $this->getQueryFromFile('radios.gql');
    $this->assertResults($query, ['webform_id' => 'graphql_webform_test_form'], [
      'form' => [
        'title' => 'GraphQL Webform test form',
        'elements' => [
          7 => [
            '__typename' => 'WebformElementRadios',
            'id' => 'radios',
            'title' => 'Radios',
            'description' => 'Choose your favorite station.',
            'options' => [
              0 => [
                'title' => 'Radio Tirana -- Broadcasting from Albania since 1937',
                'value' => 'tirana',
                '__typename' => 'WebformElementOption',
              ],
              1 => [
                'title' => 'Radio Pacifico -- Broadcasts from Peru since 1964',
                'value' => 'pacifico',
                '__typename' => 'WebformElementOption',
              ],
            ],
          ],
        ],
      ],
    ], $this->defaultCacheMetaData());
  }

}
