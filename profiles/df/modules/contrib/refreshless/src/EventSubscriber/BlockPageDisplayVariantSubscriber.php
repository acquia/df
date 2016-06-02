<?php

namespace Drupal\refreshless\EventSubscriber;

use Drupal\Core\Render\PageDisplayVariantSelectionEvent;
use Drupal\Core\Render\RenderEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * @see \Drupal\refreshless\Plugin\DisplayVariant\RefreshlessBlockPageVariant
 */
class BlockPageDisplayVariantSubscriber implements EventSubscriberInterface {

  /**
   * Selects the refreshless override of the block page display variant.
   *
   * @param \Drupal\Core\Render\PageDisplayVariantSelectionEvent $event
   *   The event to process.
   */
  public function onBlockPageDisplayVariantSelected(PageDisplayVariantSelectionEvent $event) {
    if ($event->getPluginId() === 'block_page') {
      $event->setPluginId('refreshless_block_page');
    }
  }

  /**
   * {@inheritdoc}
   */
  static function getSubscribedEvents() {
    // Set a very low priority, so that it runs last.
    $events[RenderEvents::SELECT_PAGE_DISPLAY_VARIANT][] = ['onBlockPageDisplayVariantSelected', -1000];
    return $events;
  }

}
