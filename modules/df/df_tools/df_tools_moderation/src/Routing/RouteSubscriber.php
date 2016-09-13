<?php

namespace Drupal\df_tools_moderation\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Drupal\Core\Routing\RoutingEvents;
use Symfony\Component\Routing\RouteCollection;

/**
 * Class RouteSubscriber.
 *
 * @package Drupal\df_tools_moderation\Routing
 * Listens to the dynamic route events.
 */
class RouteSubscriber extends RouteSubscriberBase {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[RoutingEvents::ALTER] = ['onAlterRoutes',-9999];  // negative Values means "late"
    return $events;
  }

  /**
   * {@inheritdoc}
   */
  protected function alterRoutes(RouteCollection $collection) {
    $collection->get('entity.node.latest_version')->setRequirements([
      '_entity_access' => 'node.view',
      '_permission' => 'view latest version,view any unpublished content',
      'node' => "\d+",
      '_method' => 'GET|POST|OPTIONS'
    ]);
  }
}
