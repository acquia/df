<?php
namespace Drupal\df_tools_services\EventSubscriber;

use Drupal\Core\Routing\RouteBuildEvent;
use Drupal\Core\Routing\RoutingEvents;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class DFToolsServicesSubscriber implements EventSubscriberInterface {

  /**
   * Forces CORS enablement for all responses.
   *
   * @param ResponseEvent $event
   */
  public function allowCORSResponse(ResponseEvent $event) {
    $response = $event->getResponse();
    $request = $event->getRequest();

    // Forcibly allow CORS for the purposes of the headless demo.
    if ($request->getMethod() == 'OPTIONS') {
      $response = new Response();
      $this->setCORSHeaders($response);
      $response->send();
      exit;
    }

    if ($response) {
      $this->setCORSHeaders($response);
    }
  }

  /**
   * Allows OPTIONS as an acceptable method for all Drupal routes.
   *
   * @param RouteBuildEvent $event
   */
  public function allowOPTIONSRoute(RouteBuildEvent $event) {
    $collection = $event->getRouteCollection();
    $routes = $collection->all();
    foreach ($routes as $name => $route) {
      $methods = $route->getMethods();
      if (!in_array('OPTIONS', $methods)) {
        $methods[] = 'OPTIONS';
        $route->setMethods($methods);
        $collection->add($name, $route);
      }
    }
  }

  /**
   * Sets headers required to enable CORS.
   *
   * @param Response $response
   */
  protected function setCORSHeaders($response) {
    $response->headers->set('Access-Control-Allow-Origin', '*');
    $response->headers->set('Access-Control-Allow-Methods', 'GET,POST,PUT,PATCH,DELETE,OPTIONS');
    $response->headers->set('Access-Control-Allow-Headers', 'Authorization, Origin, Accept, Content-Type, X-CSRF-Token');
    $response->headers->set('Access-Control-Allow-Credentials', 'true');
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events = [];
    $events[KernelEvents::RESPONSE][] = ['allowCORSResponse'];
    $events[RoutingEvents::ALTER][] = ['allowOPTIONSRoute'];
    return $events;
  }

}
