<?php

namespace Drupal\Tests\video_embed_field\Functional;

use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\simpletest\ContentTypeCreationTrait;
use Drupal\simpletest\NodeCreationTrait;
use Drupal\Tests\BrowserTestBase;

/**
 * Test the video embed field widget.
 */
abstract class FunctionalTestBase extends BrowserTestBase {

  use ContentTypeCreationTrait;
  use NodeCreationTrait;
  use AssertionsTrait;

  /**
   * A user with permission to administer content types, node fields, etc.
   *
   * @var \Drupal\user\UserInterface
   */
  protected $adminUser;

  /**
   * The field name.
   *
   * @var string
   */
  protected $fieldName;

  /**
   * The name of the content type.
   *
   * @var string
   */
  protected $contentTypeName;

  /**
   * The entity display.
   *
   * @var \Drupal\Core\Entity\Display\EntityViewDisplayInterface
   */
  protected $entityDisplay;

  /**
   * The form display.
   *
   * @var \Drupal\Core\Entity\Display\EntityFormDisplayInterface
   */
  protected $entityFormDisplay;

  /**
   * Modules to install.
   *
   * @var array
   */
  public static $modules = [
    'video_embed_field',
    'field_ui',
    'node',
    'image',
  ];

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();
    $this->fieldName = strtolower($this->randomMachineName());
    $this->contentTypeName = strtolower($this->randomMachineName());
    $this->createContentType(['type' => $this->contentTypeName]);
    $this->adminUser = $this->drupalCreateUser(array_keys($this->container->get('user.permissions')->getPermissions()));
    $field_storage = FieldStorageConfig::create([
      'field_name' => $this->fieldName,
      'entity_type' => 'node',
      'type' => 'video_embed_field',
      'settings' => [
        'allowed_providers' => [],
      ],
    ]);
    $field_storage->save();
    FieldConfig::create([
      'field_storage' => $field_storage,
      'bundle' => $this->contentTypeName,
      'settings' => [],
    ])->save();
    $this->entityDisplay = entity_get_display('node', $this->contentTypeName, 'default');
    $this->entityFormDisplay = entity_get_form_display('node', $this->contentTypeName, 'default');
    $this->resetAll();
  }

  /**
   * Set a field value.
   *
   * @param string $field
   *   The name of the field to set.
   * @param string $value
   *   The value to set.
   */
  protected function setFieldValue($field, $value) {
    $this->find('[name="' . $field . '"]')->setValue($value);
  }

  /**
   * Find an element based on a CSS selector.
   *
   * @param string $css_selector
   *   A css selector to find an element for.
   *
   * @return \Behat\Mink\Element\NodeElement|null
   *   The found element or null.
   */
  protected function find($css_selector) {
    return $this->getSession()->getPage()->find('css', $css_selector);
  }

}
