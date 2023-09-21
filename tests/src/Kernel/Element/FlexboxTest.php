<?php

declare(strict_types = 1);

namespace Drupal\Tests\graphql_webform\Kernel\Element;

use Drupal\Tests\graphql_webform\Kernel\GraphQLWebformKernelTestBase;

/**
 * Tests for the WebformElementFlexbox type.
 *
 * @group graphql_webform
 */
class FlexboxTest extends GraphQLWebformKernelTestBase {

  /**
   * Tests the flexbox element.
   */
  public function testFlexbox(): void {
    // @todo Figure out why this particular test fails on the Drupal CI.
    $this->markTestSkipped('Fails for mysterious reasons on the drupal.org test infrastructure.');
    $query = $this->getQueryFromFile('flexbox.gql');
    $this->assertResults($query, ['webform_id' => 'graphql_webform_test_form'], [
      'form' => [
        'title' => 'GraphQL Webform test form',
        'elements' => [
          10 => [
            '__typename' => 'WebformElementFlexbox',
            'id' => 'flexbox',
            'alignItems' => 'center',
            'elements' => [
              0 => ['id' => 'checkbox'],
              1 => ['id' => 'time'],
            ],
          ],
        ],
      ],
    ], $this->defaultCacheMetaData());
  }

}
