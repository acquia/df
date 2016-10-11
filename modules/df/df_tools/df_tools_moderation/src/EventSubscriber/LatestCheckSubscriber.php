<?php

namespace Drupal\df_tools_moderation\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Drupal\Core\Cache\CacheTagsInvalidator;
use Drupal\Core\Theme\ThemeManager;


/**
 * Class LatestCheckSubscriber.
 *
 * @package Drupal\df_tools_moderation
 */
class LatestCheckSubscriber implements EventSubscriberInterface {

  /**
   * Drupal\Core\Cache\CacheTagsInvalidator definition.
   *
   * @var \Drupal\Core\Cache\CacheTagsInvalidator
   */
  protected $cacheTagsInvalidator;

  /**
   * Drupal\Core\Theme\ThemeManager definition.
   *
   * @var \Drupal\Core\Theme\ThemeManager
   */
  protected $themeManager;

  /**
   * Constructor.
   */
  public function __construct(CacheTagsInvalidator $cache_tags_invalidator, ThemeManager $theme_manager) {
    $this->cacheTagsInvalidator = $cache_tags_invalidator;
    $this->themeManager = $theme_manager;
  }

  /**
   * {@inheritdoc}
   */
  static function getSubscribedEvents() {
    $events[KernelEvents::REQUEST][] = array('checkForLatest');
    return $events;
  }

  /**
   * Given 'node' in the request params,
   * and the current user can view 'latest' revisions:
   * Invalidate the current theme's local actions cache,
   * so that hook_menu_local_tasks_alter() is called.
   *
   * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
   */
  public function checkForLatest(GetResponseEvent $event) {
    if ($node = $event->getRequest()->get('node')) {
      if (\Drupal::currentUser()->hasPermission('view latest version')) {
        $theme = $this->themeManager->getActiveTheme()->getName();
        $tag = 'config:block.block.' . $theme . '_local_actions';
        $this->cacheTagsInvalidator->invalidateTags([$tag]);
      }
    }
  }

}
