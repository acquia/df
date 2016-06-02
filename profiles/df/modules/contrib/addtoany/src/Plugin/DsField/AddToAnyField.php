<?php

/**
 * @file
 * Contains \Drupal\ds\Plugin\DsField\DsFieldBase.
 */

namespace Drupal\addtoany\Plugin\DsField;
use Drupal\ds\Plugin\DsField\DsFieldBase;

/**
 * Plugin that renders the AddToAny Buttons as Display Suite Field.
 *
 * @DsField(
 *   id = "addtoany_field",
 *   title = @Translation("AddToAny Buttons"),
 *   entity_type = "node",
 *   provider = "addtoany",
 *   ui_limit = {"*|*"}
 * )
 */
class AddToAnyField extends DsFieldBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $node = \Drupal::routeMatch()->getParameter('node');

    return array(
      '#theme' => 'addtoany_standard',
      '#addtoany_html' => addtoany_create_node_buttons($node),
    );
  }
}
