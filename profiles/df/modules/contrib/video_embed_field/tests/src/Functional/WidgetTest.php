<?php

namespace Drupal\Tests\video_embed_field\Functional;

use Drupal\Core\Url;

/**
 * Test the video embed field widget.
 *
 * @group video_embed_field
 */
class WidgetTest extends FunctionalTestBase {

  /**
   * Test the input widget.
   */
  public function testVideoEmbedFieldDefaultWidget() {
    $this->entityFormDisplay
      ->setComponent($this->fieldName, ['type' => 'video_embed_field_textfield'])
      ->save();

    $this->drupalLogin($this->adminUser);
    $node_title = $this->randomMachineName();

    // Test an invalid input.
    $this->drupalGet(Url::fromRoute('node.add', ['node_type' => $this->contentTypeName])->toString());
    $this->submitForm([
      'title[0][value]' => $node_title,
      $this->fieldName . '[0][value]' => 'Some useless value.',
    ], t('Save and publish'));
    $this->assertRaw(t('Could not find a video provider to handle the given URL.'));

    // Test a valid input.
    $valid_input = 'https://vimeo.com/80896303';
    $this->submitForm([
      $this->fieldName . '[0][value]' => $valid_input,
    ], t('Save and publish'));
    $this->assertRaw(t('@type %title has been created.', [
      '@type' => $this->contentTypeName,
      '%title' => $node_title,
    ]));

    // Load the saved node and assert the valid value was saved into the field.
    $nodes = \Drupal::entityTypeManager()
      ->getStorage('node')
      ->loadByProperties(['title' => $node_title]);
    $node = array_shift($nodes);
    $this->assertEquals($node->{$this->fieldName}[0]->value, $valid_input);
  }

}
