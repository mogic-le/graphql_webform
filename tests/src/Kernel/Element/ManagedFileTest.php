<?php

declare(strict_types = 1);

namespace Drupal\Tests\graphql_webform\Kernel\Element;

use Drupal\Tests\graphql_webform\Kernel\GraphQLWebformKernelTestBase;

/**
 * Tests for the WebformElementManagedFile type.
 *
 * @group graphql_webform
 */
class ManagedFileTest extends GraphQLWebformKernelTestBase {

  /**
   * Tests managed files.
   */
  public function testManagedFile(): void {
    $query = $this->getQueryFromFile('managed_file.gql');
    $this->assertResults($query, ['webform_id' => 'graphql_webform_test_form'], [
      'form' => [
        'title' => 'GraphQL Webform test form',
        'elements' => [
          6 => [
            '__typename' => 'WebformElementManagedFile',
            'id' => 'file_upload',
            'title' => 'File upload',
            'description' => 'Description',
            'maxFilesize' => '2',
            'fileExtensions' => 'gif jpg png txt',
            'multiple' => [
              'limit' => '0',
            ],
          ],
        ],
      ],
    ], $this->defaultCacheMetaData());
  }

}
