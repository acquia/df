<?php

/**
 * @file
 * Contains \Drupal\refreshless\EventSubscriber\HtmlResponseSubscriber.
 */

namespace Drupal\refreshless\EventSubscriber;

use Drupal\Core\Render\HtmlResponse;
use Drupal\refreshless\RefreshlessPageState;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Response subscriber to process HTML responses for Refreshless.
 *
 * All modifications this makes are harmless: they don't ever break HTML or
 * normal site operation. Even if JavaScript is turned
 */
class HtmlResponseSubscriber implements EventSubscriberInterface {

  /**
   * Constructs a HtmlResponseSubscriber object.
   *
   * @param \Drupal\refreshless\RefreshlessPageState $refreshless_page_state
   */
  public function __construct(RefreshlessPageState $refreshless_page_state) {
    $this->refreshlessPageState = $refreshless_page_state;
  }

  /**
   * Processes HTML responses to allow Refreshless' JavaScript to work.
   *
   * @param \Symfony\Component\HttpKernel\Event\FilterResponseEvent $event
   *   The event to process.
   */
  public function onRespond(FilterResponseEvent $event) {
    $response = $event->getResponse();
    if (!$response instanceof HtmlResponse) {
      return;
    }

    $this->wrapHeadAttachmentsInMarkers($response);
    $this->initializeRefreshlessPageState($response);
  }

  /**
   * Wraps the <head> attachments placeholder in markers.
   *
   * @param \Drupal\Core\Render\HtmlResponse $response
   *   The HTML response to update.
   *
   * @see \Drupal\refreshless\Ajax\RefreshlessUpdateHtmlHeadCommand
   */
  protected function wrapHeadAttachmentsInMarkers(HtmlResponse $response) {
    // Wrap the head placeholder with a marker before and after,
    // because the JS for RefreshlessUpdateHtmlHeadCommand needs to be able to
    // replace that markup when navigating using Refreshless.
    $attachments = $response->getAttachments();
    if (isset($attachments['html_response_attachment_placeholders']['head'])) {
      $head_placeholder = $attachments['html_response_attachment_placeholders']['head'];
      $content = $response->getContent();
      $content = str_replace($head_placeholder, '<meta name="refreshless-head-marker-start" />' . "\n" . $head_placeholder . "\n" . '<meta name="refreshless-head-marker-stop" />', $content);
      $response->setContent($content);
    }
  }

  /**
   * Initializes Refreshless page state.
   *
   * @param \Drupal\Core\Render\HtmlResponse $response
   *   The HTML response to update.
   */
  protected function initializeRefreshlessPageState(HtmlResponse $response) {
    $response->addAttachments(['drupalSettings' => ['refreshlessPageState' => $this->refreshlessPageState->build($response->getCacheableMetadata())]]);
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    // Run just before \Drupal\Core\EventSubscriber\HtmlResponseSubscriber
    // (priority 0), which invokes
    // \Drupal\Core\Render\HtmlResponseAttachmentsProcessor::processAttachments,
    // which is what processes all attachments into a final HTML response.
    $events[KernelEvents::RESPONSE][] = ['onRespond', 1];

    return $events;
  }

}
