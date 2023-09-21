<?php

declare(strict_types = 1);

namespace Drupal\Tests\graphql_webform\Kernel\Element;

use Drupal\graphql\GraphQL\Execution\QueryResult;
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
          [
            '__typename' => 'WebformElementActions',
            'submitLabel' => 'Set sail for adventure',
          ],
        ],
      ],
    ], $this->defaultCacheMetaData());
  }

  /**
   * {@inheritdoc}
   */
  protected function assertResultData(QueryResult $result, $expected): void {
    $data = $result->toArray();
    // The actions are always at the end of the form. We can safely remove all
    // other elements from the result. This makes it easier to maintain the
    // tests since we don't always need to update the element order when adding
    // new elements to the test form.
    $elements = &$data['data']['form']['elements'];
    $elements = array_slice($elements, -1, 1, FALSE);

    $result = new QueryResult($data['data'], $data['errors'] ?? [], $data['extensions'] ?? []);
    parent::assertResultData($result, $expected);
  }

}
