<?php

declare(strict_types = 1);

namespace Drupal\Tests\graphql_webform\Kernel\Mutation;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Tests\graphql_webform\Kernel\GraphQLWebformKernelTestBase;
use Drupal\webform\WebformSubmissionStorageInterface;

/**
 * Tests submission of a webform.
 *
 * @group graphql_webform
 */
class FormSubmissionTest extends GraphQLWebformKernelTestBase {

  /**
   * The entity type manager.
   */
  protected ?EntityTypeManagerInterface $entityTypeManager;

  /**
   * The webform submission storage.
   */
  protected ?WebformSubmissionStorageInterface $webformSubmissionStorage;

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    // The webform module requires the File module when a form allows file
    // uploads.
    'file',
    // The webform module depends silently on the Pathalias module, which is
    // fine since this is a required module.
    'path_alias',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installEntitySchema('user');
    $this->installEntitySchema('webform_submission');

    $this->entityTypeManager = $this->container->get('entity_type.manager');
    $this->webformSubmissionStorage = $this->entityTypeManager->getStorage('webform_submission');
  }

  /**
   * Tests submitting a form with a required field omitted.
   *
   * This should return the required text that was configured in the webform.
   */
  public function testOmitRequiredField(): void {
    $query = $this->getQueryFromFile('invalid_submission.gql');
    $values = (object) [
      'webform_id' => 'graphql_webform_test_form',
    ];
    $variables = ['values' => json_encode($values)];

    $this->assertResults($query, $variables, [
      'submitForm' => [
        'errors' => ['This field is required because it is important.'],
        'submission' => NULL,
      ],
    ], $this->defaultCacheMetaData()->setCacheTags(['graphql']));

    $this->assertWebformSubmissionCount(0, 'An invalid submission is not saved.');
  }

  /**
   * Tests a valid form submission.
   */
  public function testValidSubmission(): void {
    $query = $this->getQueryFromFile('invalid_submission.gql');
    $values = (object) [
      'webform_id' => 'graphql_webform_test_form',
      'required_text_field' => 'This is a valid value.',
    ];
    $variables = ['values' => json_encode($values)];

    $this->assertResults($query, $variables, [
      'submitForm' => [
        'errors' => [],
        'submission' => [
          'id' => '1',
        ],
      ],
    ], $this->defaultCacheMetaData()->setCacheTags(['graphql']));

    // There should be one submission in the database.
    $this->assertWebformSubmissionCount(1, 'A valid submission is saved.');

    // Load the webform submission and check it contains the expected value.
    $submissions = $this->webformSubmissionStorage->loadMultiple();
    $submission = reset($submissions);
    $this->assertEquals('This is a valid value.', $submission->getElementData('required_text_field'));
  }

  /**
   * {@inheritdoc}
   */
  protected function defaultCacheMaxAge(): int {
    // Since these are mutations we don't want to cache the results. A
    // subsequent request will probably have different results.
    return 0;
  }

  /**
   * Checks the number of webform submissions.
   *
   * @param int $expected_count
   *   The expected number of webform submissions.
   * @param string $message
   *   The assertion message.
   */
  protected function assertWebformSubmissionCount(int $expected_count, string $message = ''): void {
    $count = $this->webformSubmissionStorage->getTotal();
    $this->assertEquals($expected_count, $count, $message);
  }

}
