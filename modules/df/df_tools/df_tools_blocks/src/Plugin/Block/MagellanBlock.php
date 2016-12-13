<?php

namespace Drupal\df_tools_blocks\Plugin\Block;

use Drupal\Component\Utility\Html;
use Drupal\Core\Block\BlockBase;

/**
 * Provides the "Magellan" block.
 *
 * @Block(
 *   id = "magellan",
 *   admin_label = @Translation("Magellan"),
 *   category = @Translation("Menus")
 * )
 */
class MagellanBlock extends BlockBase {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $anchor_id = Html::getUniqueId('df-tools-anchor');
    $build = [
      '#markup' =>
        '<div id="' . $anchor_id . '"></div>' .
        '<div data-sticky-container class="df-tools-magellan-block">' .
        '  <div class="sticky" data-sticky data-top-anchor="' . $anchor_id . '" data-margin-top="0">' .
        '    <nav data-magellan data-active-class="active">' .
        '      <ul class="horizontal menu expanded"></ul>' .
        '    </nav>' .
        '  </div>' .
        '</div>'
    ];

    $build['#attached']['library'][] = 'df_tools_blocks/magellan';

    return $build;
  }

}
