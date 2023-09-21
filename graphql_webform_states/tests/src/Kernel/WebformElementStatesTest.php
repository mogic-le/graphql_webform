<?php

declare(strict_types = 1);

namespace Drupal\Tests\graphql_webform_states\Kernel\Element;

use Drupal\graphql\GraphQL\Execution\QueryResult;
use Drupal\Tests\graphql_webform\Kernel\GraphQLWebformKernelTestBase;

/**
 * Tests retrieving information about webform element states.
 *
 * @group graphql_webform_states
 */
class WebformElementStatesTest extends GraphQLWebformKernelTestBase {

  /**
   * {@inheritdoc}
   */
  public static $modules = [
    'graphql_webform_states',
    'graphql_webform_states_test',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp(): void {
    parent::setUp();

    $this->installConfig('graphql_webform_states_test');
  }

  /**
   * Tests some common states.
   */
  public function testStates(): void {
    $query = $this->getQueryFromFile('states.gql');
    $this->assertResults($query, ['webform_id' => 'graphql_webform_states_test_form'], [
      'form' => [
        'title' => 'GraphQL Webform States test form',
        'elements' => [
          1 => [
            'id' => 'email',
            'states' => [
              'visibleSlide' => [
                'conditions' => [
                  0 => [
                    'value' => 'Email',
                    'selector' => ':input[name="authenticate_using"]',
                    'field' => 'authenticate_using',
                    'fieldValue' => 'Email',
                    'trigger' => 'VALUE_IS',
                  ],
                ],
                'logic' => 'AND',
              ],
              'required' => [
                'conditions' => [
                  0 => [
                    'value' => 'Email',
                    'selector' => ':input[name="authenticate_using"]',
                    'field' => 'authenticate_using',
                    'fieldValue' => 'Email',
                    'trigger' => 'VALUE_IS',
                  ],
                ],
                'logic' => 'AND',
              ],
            ],
          ],
          2 => [
            'id' => 'receive_email',
            'states' => [
              'visible' => [
                'conditions' => [
                  0 => [
                    'value' => '1',
                    'selector' => ':input[name="email"]',
                    'field' => 'email',
                    'fieldValue' => '1',
                    'trigger' => 'FILLED',
                  ],
                ],
                'logic' => 'AND',
              ],
            ],
          ],
          4 => [
            'id' => 'phone_number',
            'states' => [
              'visibleSlide' => [
                'conditions' => [
                  0 => [
                    'value' => '1',
                    'selector' => ':input[name="show_phone_number_on_profile_page"]',
                    'field' => 'show_phone_number_on_profile_page',
                    'fieldValue' => '1',
                    'trigger' => 'CHECKED',
                  ],
                  1 => [
                    'value' => 'Email',
                    'selector' => ':input[name="authenticate_using"]',
                    'field' => 'authenticate_using',
                    'fieldValue' => 'Email',
                    'trigger' => 'VALUE_IS_NOT',
                  ],
                ],
                'logic' => 'OR',
              ],
            ],
          ],
          6 => [
            'id' => 'car_license',
            'states' => [
              'enabled' => [
                'conditions' => [
                  0 => [
                    'value' => '18',
                    'selector' => ':input[name="age"]',
                    'field' => 'age',
                    'fieldValue' => '18',
                    'trigger' => 'GREATER_THAN_OR_EQUAL_TO',
                  ],
                ],
                'logic' => 'AND',
              ],
              'required' => [
                'conditions' => [
                  0 => [
                    'value' => 'Car license',
                    'selector' => ':input[name="authenticate_using"]',
                    'field' => 'authenticate_using',
                    'fieldValue' => 'Car license',
                    'trigger' => 'VALUE_IS',
                  ],
                ],
                'logic' => 'AND',
              ],
            ],
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
    // Filter out all fields that do not have any states. These can be ignored
    // in this test.
    if (!empty($data['data']['form']['elements'])) {
      $elements = &$data['data']['form']['elements'];
      $elements = array_filter($elements, function ($value) {
        return !empty((array) $value['states'] ?? []);
      });
      // Filter out unused states.
      foreach ($elements as &$element) {
        $element['states'] = array_filter($element['states'], function ($value) {
          return !empty($value);
        });
      }
    }

    $this->assertArrayHasKey('data', $data, 'No result data.');
    $this->assertEquals($expected, $data['data'], 'Unexpected query result.');
  }

  /**
   * {@inheritdoc}
   */
  protected function defaultCacheTags() {
    return [
      'config:webform.settings',
      'config:webform.webform.graphql_webform_states_test_form',
      'graphql',
      'webform:graphql_webform_states_test_form',
    ];
  }

}
