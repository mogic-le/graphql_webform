<?php

declare(strict_types = 1);

namespace Drupal\Tests\graphql_webform\Kernel\Element;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Language\LanguageInterface;
use Drupal\taxonomy\Entity\Term;
use Drupal\taxonomy\Entity\Vocabulary;
use Drupal\taxonomy\TermInterface;
use Drupal\taxonomy\VocabularyInterface;
use Drupal\Tests\graphql_webform\Kernel\GraphQLWebformKernelTestBase;

/**
 * Tests for the WebformElementTermSelect type.
 *
 * @group graphql_webform
 */
class TermSelectTest extends GraphQLWebformKernelTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'graphql_core',
    'path_alias',
    'taxonomy',
    'text',
  ];

  /**
   * A test vocabulary.
   *
   * @var \Drupal\taxonomy\VocabularyInterface|null
   */
  protected ?VocabularyInterface $vocabulary;

  /**
   * Test terms, keyed by name.
   *
   * @var \Drupal\taxonomy\TermInterface[]
   */
  protected array $terms = [];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    // Set up the Taxonomy module.
    $this->installEntitySchema('taxonomy_term');
    $this->installEntitySchema('taxonomy_vocabulary');
    $this->installEntitySchema('user');
    $this->installConfig(['taxonomy']);

    // Create the 'Tags' vocabulary which is used in the test webform.
    $this->vocabulary = Vocabulary::create([
      'vid' => 'tags',
    ]);
    $this->vocabulary->save();

    // Create a hierarchy of test terms.
    $terms = ['A' => ['B1', 'B2']];
    foreach ($terms as $parent => $children) {
      $this->terms[$parent] = $this->createTerm($parent);
      foreach ($children as $child) {
        $this->terms[$child] = $this->createTerm($child, $this->terms[$parent]);
      }
    }
  }

  /**
   * Tests the term select element with different depths.
   *
   * @dataProvider termSelectDepthProvider
   */
  public function testTermSelect(int $depth, array $expected_terms): void {
    $query = $this->getQueryFromFile('term_select.gql');
    $expected_term_options = array_map(fn (string $term_name): array => ['name' => $term_name], $expected_terms);
    $this->assertResults($query, [
      'webform_id' => 'graphql_webform_test_form',
      'depth' => $depth,
    ], [
      'form' => [
        'title' => 'GraphQL Webform test form',
        'elements' => [
          9 => [
            '__typename' => 'WebformElementTermSelect',
            'id' => 'term_select',
            'title' => 'Term select',
            'description' => 'Select one or two tags.',
            'multiple' => ['limit' => 2],
            'termOptions' => $expected_term_options,
          ],
        ],
      ],
    ], $this->defaultCacheMetaData($expected_terms));
  }

  /**
   * Data provider for testTermSelect().
   *
   * @return array[]
   *   An array of test cases. Each test case an array with 2 elements:
   *   - The depth of terms to select.
   *   - The expected terms that should be returned.
   */
  public function termSelectDepthProvider(): array {
    return [
      [1, ['A']],
      [2, ['A', 'B1', 'B2']],
    ];
  }

  /**
   * {@inheritdoc}
   */
  protected function defaultCacheMetaData(array $expected_terms = []) {
    $metadata = new CacheableMetadata();
    $metadata->setCacheMaxAge($this->defaultCacheMaxAge());
    $metadata->setCacheTags($this->defaultCacheTags($expected_terms));
    $metadata->setCacheContexts($this->defaultCacheContexts());
    return $metadata;
  }

  /**
   * {@inheritdoc}
   */
  protected function defaultCacheTags(array $expected_terms = []): array {
    // We expect the cache tags to include the test terms.
    return array_merge(
      array_map(fn (string $term): string => 'taxonomy_term:' . $this->terms[$term]->id(), $expected_terms),
      parent::defaultCacheTags()
    );
  }

  /**
   * Creates a test taxonomy term.
   *
   * @param string $name
   *   The name of the term.
   * @param \Drupal\taxonomy\TermInterface|null $parent
   *   Optional parent term.
   *
   * @return \Drupal\taxonomy\TermInterface
   *   The created term.
   */
  protected function createTerm(string $name, ?TermInterface $parent = NULL): TermInterface {
    $term = Term::create([
      'name' => $name,
      'vid' => 'tags',
      'langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED,
      'parent' => $parent,
    ]);
    $term->save();

    return $term;
  }

}
