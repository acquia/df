<?php

/**
 * @file
 * Contains \Drupal\scheduler\Tests\SchedulerFieldsDisplayTest
 */

namespace Drupal\scheduler\Tests;

use Drupal\node\Entity\NodeType;

/**
 * Tests the components of the Scheduler interface which use the Date module.
 *
 * @group scheduler
 */
class SchedulerFieldsDisplayTest extends SchedulerTestBase {

  /**
   * SchedulerTestBase loads the standard modules.
   * Additional module field_ui is required for the 'manage form display' test.
   */
  public static $modules = ['field_ui'];

  /**
   * Tests date input is displayed as vertical tab or an expandable fieldset.
   */
  public function testFieldsDisplay() {
    /** @var NodeTypeInterface $node_type */
    $node_type = NodeType::load('page');
    $this->drupalLogin($this->adminUser);

    // Check that the dates are shown in a vertical tab by default.
    $this->drupalGet('node/add/page');
    $this->assertTrue($this->xpath('//div[contains(@class, "form-type-vertical-tabs")]//details[@id = "edit-scheduler-settings"]'), 'By default the scheduler dates are shown in a vertical tab.');

    // Check that the dates are shown as a fieldset when configured to do so.
    $node_type->setThirdPartySetting('scheduler', 'fields_display_mode', 'fieldset')->save();
    $this->drupalGet('node/add/page');
    $this->assertFalse($this->xpath('//div[contains(@class, "form-type-vertical-tabs")]//details[@id = "edit-scheduler-settings"]'), 'The scheduler dates are not shown in a vertical tab when they are configured to show as a fieldset.');
    $this->assertTrue($this->xpath('//details[@id = "edit-scheduler-settings"]'), 'The scheduler dates are shown in a fieldset when they are configured to show as a fieldset.');
    $this->assertTrue($this->xpath('//details[@id = "edit-scheduler-settings" and not(@open = "open")]'), 'The scheduler dates fieldset is collapsed by default.');

    // Check that the fieldset is expanded if either of the scheduling dates
    // are required.
    $node_type->setThirdPartySetting('scheduler', 'publish_required', TRUE)->save();
    $this->drupalGet('node/add/page');
    $this->assertTrue($this->xpath('//details[@id = "edit-scheduler-settings" and @open = "open"]'), 'The scheduler dates are shown in an expanded fieldset when the publish-on date is required.');

    $node_type->setThirdPartySetting('scheduler', 'publish_required', FALSE)
              ->setThirdPartySetting('scheduler', 'unpublish_required', TRUE)->save();
    $this->drupalGet('node/add/page');
    $this->assertTrue($this->xpath('//details[@id = "edit-scheduler-settings" and @open = "open"]'), 'The scheduler dates are shown in an expanded fieldset when the unpublish-on date is required.');

    // Check that the fieldset is expanded if the 'always' option is set.
    $node_type->setThirdPartySetting('scheduler', 'publish_required', FALSE)
              ->setThirdPartySetting('scheduler', 'unpublish_required', FALSE)
              ->setThirdPartySetting('scheduler', 'expand_fieldset', 'always')->save();
    $this->drupalGet('node/add/page');
    $this->assertTrue($this->xpath('//details[@id = "edit-scheduler-settings" and @open = "open"]'), 'The scheduler dates are shown in an expanded fieldset when the option to always expand is turned on.');

    // Check that the fieldset is expanded if the node already has a publish-on
    // date. This requires editing an existing scheduled node.
    $node_type->setThirdPartySetting('scheduler', 'expand_fieldset', 'when_required')->save();
    $options = [
      'title' => 'Contains Publish-on date ' . $this->randomMachineName(10),
      'type' => 'page',
      'publish_on' => strtotime('+1 day'),
    ];
    $node = $this->drupalCreateNode($options);
    $this->drupalGet('node/' . $node->id() . '/edit');
    $this->assertTrue($this->xpath('//details[@id = "edit-scheduler-settings" and @open = "open"]'), 'The scheduler dates are shown in an expanded fieldset when a publish-on date already exists.');

    // Check that the fieldset is expanded if the node has an unpublish-on date.
    $options = [
      'title' => 'Contains Unpublish-on date ' . $this->randomMachineName(10),
      'type' => 'page',
      'unpublish_on' => strtotime('+1 day'),
    ];
    $node = $this->drupalCreateNode($options);
    $this->drupalGet('node/' . $node->id() . '/edit');
    $this->assertTrue($this->xpath('//details[@id = "edit-scheduler-settings" and @open = "open"]'), 'The scheduler dates are shown in an expanded fieldset when an unpublish-on date already exists.');
  }

  /**
   * Tests the settings entry in the content type form display.
   * This test covers scheduler_entity_extra_field_info().
   */
  public function testManageFormDisplay() {

    // Create a custom administrator user with permissions to use the field_ui
    // module 'node form display' tab.
    $this->adminUser = $this->drupalCreateUser([
      'administer content types',
      'administer node form display',
    ]);
    $this->drupalLogin($this->adminUser);

    // Check that the weight input field is displayed when the content type is
    // enabled for scheduling.
    $this->drupalGet('admin/structure/types/manage/' . $this->nodetype->get('type') . '/form-display');
    $this->assertFieldByName('fields[scheduler_settings][weight]', '', 'The scheduler settings weight entry is shown when the content type is enabled for scheduling.');

    // Check that the weight input field is not displayed when the content type
    // is not enabled for scheduling.
    $this->nodetype->setThirdPartySetting('scheduler', 'publish_enable', FALSE)
      ->setThirdPartySetting('scheduler', 'unpublish_enable', FALSE)->save();
    $this->drupalGet('admin/structure/types/manage/' . $this->nodetype->get('type') . '/form-display');
    $this->assertNoFieldByName('fields[scheduler_settings][weight]', '', 'The scheduler settings weight entry is not shown when the content type is not enabled for scheduling.');
  }
}
