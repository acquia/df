<?php

namespace Drupal\refreshless\EventSubscriber;

use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\EventSubscriber\MainContentViewSubscriber;
use Drupal\Core\Routing\LocalRedirectResponse;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Response subscriber to process redirect responses for RefreshLess.
 *
 * Ensures that redirect responses sent in response to a request that has the
 * RefreshLess wrapper format persist the entire query string, otherwise the
 * redirected URL won't receive the necessary state metadata when requested.
 *
 * @see \Drupal\Core\EventSubscriber\RedirectResponseSubscriber
 */
class RedirectResponseSubscriber implements EventSubscriberInterface {

  /**
   * Ensures that requests with wrapper formats also have functioning redirects.
   *
   * @param \Symfony\Component\HttpKernel\Event\FilterResponseEvent $event
   *   The Event to process.
   */
  public function onRedirectResponse(FilterResponseEvent $event) {
    $response = $event->getResponse();
    // Only local redirects need to be updated to have the wrapper format.
    if ($response instanceof LocalRedirectResponse) {
      $request = $event->getRequest();
      if ($request->getRequestFormat() === 'html' && $request->query->get(MainContentViewSubscriber::WRAPPER_FORMAT) === 'drupal_refreshless') {
        $old_redirect_url = $response->getTargetUrl();
        $parts = parse_url($old_redirect_url);

        // We can just append the query.
        if (!isset($parts['query']) && !isset($parts['fragment'])) {
          $url = $old_redirect_url . '?' . UrlHelper::buildQuery($request->query->all());
        }
        // We need to update the existing query.
        elseif (isset($parts['query'])) {
          $query = [];
          parse_str($parts['query'], $query);
          $query += $request->query->all();
          $url = str_replace($parts['query'], UrlHelper::buildQuery($query), $old_redirect_url);
        }
        // There is no query, but we can't just append it because there is a
        // fragment in the way.
        elseif (isset($parts['fragment'])) {
          $url = str_replace('#' . $parts['fragment'], '', $old_redirect_url);
          $url .= '?' . UrlHelper::buildQuery($request->query->all());
          $url .= '#' . $parts['fragment'];
        }

        $response->setTargetUrl($url);
        $response->addCacheableDependency((new CacheableMetadata())->setCacheContexts(['url.query_args']));
      }
    }
  }

  /**
   * {@inheritdoc}
   */
  static function getSubscribedEvents() {
    // Run after \Drupal\Core\EventSubscriber\RedirectResponseSubscriber, which
    // has priority 0. This way, we can rely on any local redirects having been
    // converted to LocalRedirectResponse objects, so we don't have to check
    // whether redirects are local or not.
    $events[KernelEvents::RESPONSE][] = ['onRedirectResponse', -10];

    return $events;
  }

}
