<?php

namespace Drupal\refreshless\Plugin\DisplayVariant;

use Drupal\block\Plugin\DisplayVariant\BlockPageVariant;
use Drupal\Core\Render\Element;

/**
 * Decorates BlockPageVariant to ensure all necessary regions are printed.
 *
 * @todo In Drupal 9, make regions be rendered at all times, not only when they
 *       have non-empty contents. Then this would not be necessary.
 *
 * A region that is either completely empty or does not vary by any of the
 * Refreshless-sensitive cache contexts is fine to not be printed.
 * But, any other region must be guaranteed to have its wrapper markup printed,
 * to allow Refreshless-based navigation: otherwise it may be impossible for
 * Refreshless's JS to insert a particular region: how could it possibly know
 * where that region belonged in the DOM?
 *
 * @see \Drupal\block\Plugin\DisplayVariant\BlockPageVariant
 *
 * @PageDisplayVariant(
 *   id = "refreshless_block_page",
 *   admin_label = @Translation("Page with blocks")
 * )
 */
class RefreshlessBlockPageVariant extends BlockPageVariant {

  /**
   * {@inheritdoc}
   */
  public function build() {
    $build = parent::build();
    foreach (Element::children($build) as $region) {
      // Only completely empty regions will always be empty:
      // - the absence of blocks (child elements) indicates that the region is
      //   empty for this particular request
      // - the absence of #cache indicates that there are *never* any blocks in
      //   this region for *any* request (if there was any conditionality to it,
      //   then #cache would be populated with some cache contexts)
      if (empty($build[$region])) {
        continue;
      }

      // Always add a bit of placeholder markup to ensure the region wrapper
      // markup is rendered. Because there may not be any blocks for this
      // particular request, and if there are blocks, they may render to the
      // empty string. But for another (Refreshless) request, this region may
      // contain blocks, and then Refreshless's JavaScript needs to be able to
      // find this region in the DOM.
      $build[$region]['refreshless_trigger_region_wrapper_markup'] = ['#markup' => '<div data-refreshless-trigger-region-wrapper-markup></div>'];
    }

    return $build;
  }

}
