<?php

namespace Drupal\addtoany\Plugin\Block;

use Drupal\Core\Block\BlockBase;

/**
 * Provides an 'AddToAny' block.
 *
 * @Block(
 *   id = "addtoany_block",
 *   admin_label = @Translation("AddToAny buttons"),
 * )
 */
class AddToAnyBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $node = \Drupal::routeMatch()->getParameter('node');

    return array(
      // Not using #markup because data attribute name gets mangled
      // @see https://www.drupal.org/node/2609928
      // '#markup' => addtoany_create_node_buttons($node),
      '#addtoany_html' => addtoany_create_node_buttons($node),
      '#theme' => 'addtoany_standard',
    );
  }

}
