<?php

declare(strict_types = 1);

namespace Drupal\Tests\graphql_webform\Kernel\Element;

use Drupal\Core\File\FileSystemInterface;
use Drupal\file\FileInterface;
use Drupal\file\FileStorageInterface;
use Drupal\Tests\graphql_webform\Kernel\GraphQLWebformKernelTestBase;
use Symfony\Component\HttpFoundation\Request;

/**
 * Tests uploading a file for a webform.
 *
 * @group graphql_webform
 */
class CreateFileTest extends GraphQLWebformKernelTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'file',
  ];

  /**
   * The file storage.
   *
   * @var \Drupal\file\FileStorageInterface|null
   */
  protected ?FileStorageInterface $fileStorage;

  /**
   * The file system.
   *
   * @var \Drupal\Core\File\FileSystemInterface|null
   */
  protected ?FileSystemInterface $fileSystem;

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installEntitySchema('file');

    $this->fileStorage = $this->container->get('entity_type.manager')->getStorage('file');
    $this->fileSystem = $this->container->get('file_system');
  }

  /**
   * Tests uploading a file through the webformFileUpload mutation.
   */
  public function testFileUpload() {
    // We are pretending to upload a file into temporary storage. Ensure a file
    // is there because the Symfony UploadedFile component will check that.
    $file = $this->fileSystem->getTempDirectory() . '/graphql_webform_upload_test.txt';
    touch($file);

    // Create a post request with file contents.
    $request = Request::create('/graphql', 'POST', [
      'query' => $this->getQueryFromFile('create_file.gql'),
      // The variable has to be declared null.
      'variables' => [
        'file' => NULL,
        'webform_element_id' => 'file_upload',
        'webform_id' => 'graphql_webform_test_form',
      ],
      // Then map the file upload name to the variable.
      'map' => [
        'test' => ['variables.file'],
      ],
    ], [], [
      'test' => [
        'name' => 'test.txt',
        'type' => 'text/plain',
        'size' => 42,
        'tmp_name' => $file,
        'error' => UPLOAD_ERR_OK,
      ],
    ]);

    $request->headers->add(['content-type' => 'multipart/form-data']);
    $response = $this->container->get('http_kernel')->handle($request);
    $result = json_decode($response->getContent());

    // Check that the file ID was returned.
    $returned_fid = $result->data->webformFileUpload->fid ?? NULL;
    $this->assertIsInt($returned_fid);

    // Check that the file entity was created.
    $file = $this->fileStorage->load($returned_fid);
    $this->assertInstanceOf(FileInterface::class, $file);
    $this->assertEquals('test.txt', $file->getFilename());
    $this->assertEquals('text/plain', $file->getMimeType());
  }

}

namespace Drupal\graphql_webform\Plugin\GraphQL\Mutations;

/**
 * Mock the PHP function is_uploaded_file().
 *
 * Since we are not *really* uploading a file through the webserver, PHP will
 * not recognize the file as an uploaded file. We mock the function to return
 * TRUE for our test file.
 *
 * @param string $filename
 *   The filename being checked.
 *
 * @return bool
 *   Will return TRUE for our test file.
 */
function is_uploaded_file($filename) {
  $temp_dir = \Drupal::service('file_system')->getTempDirectory();
  $test_file = $temp_dir . '/graphql_webform_upload_test.txt';
  return $filename === $test_file;
}

namespace Drupal\Core\File;

/**
 * Mock the PHP function move_uploaded_file().
 *
 * Since we are not *really* uploading a file through the webserver, PHP will
 * refuse to move the file and will return FALSE. We mock the function to return
 * TRUE for our test file.
 *
 * @param string $filename
 *   The filename being moved.
 * @param string $destination
 *   The destination path.
 *
 * @return bool
 *   Will return TRUE for our test file.
 */
function move_uploaded_file($filename, $destination) {
  $temp_dir = \Drupal::service('file_system')->getTempDirectory();
  $test_file = $temp_dir . '/graphql_webform_upload_test.txt';
  return $filename === $test_file;
}
