<?php

/**
 * @file
 * Contains \Drupal\refreshless\Render\MainContent\AjaxRenderer.
 */

namespace Drupal\refreshless\Render\MainContent;

use Drupal\Component\Utility\Crypt;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\InsertCommand;
use Drupal\Core\Render\AttachmentsResponseProcessorInterface;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\Core\Render\HtmlResponse;
use Drupal\Core\Render\MainContent\MainContentRendererInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\refreshless\Ajax\RefreshlessUpdateHtmlHeadCommand;
use Drupal\refreshless\Ajax\RefreshlessUpdateRegionCommand;
use Drupal\refreshless\RefreshlessPageState;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\PreconditionFailedHttpException;

/**
 * Default main content renderer for Refreshless requests.
 */
class RefreshlessRenderer implements MainContentRendererInterface {

  /**
   * The attachment types destined for the HTML <head>.
   *
   * @var string[]
   *
   * @see https://developer.mozilla.org/en/docs/Web/HTML/Element/head
   */
  protected static $htmlHeadAttachmentTypes = ['html_head', 'feed', 'html_head_link'];

  /**
   * The HTML response attachments processor service.
   *
   * @var \Drupal\Core\Render\AttachmentsResponseProcessorInterface
   */
  protected $htmlResponseAttachmentsProcessor;

  public function __construct(MainContentRendererInterface $html_renderer, RefreshlessPageState $refreshless_page_state, AttachmentsResponseProcessorInterface $html_response_attachments_processor) {
    $this->htmlRenderer = $html_renderer;
    $this->renderer = \Drupal::service('renderer');
    $this->refreshlessPageState = $refreshless_page_state;
    $this->htmlResponseAttachmentsProcessor = $html_response_attachments_processor;
  }

  /**
   * Validates preconditions required to be able to respond to this request.
   *
   * Verifies:
   * - the theme remains the same (relying on Ajax page state)
   * - the theme token is valid (relying on Ajax page state)
   */
  protected function validatePreconditions(Request $request) {
    // The theme token is only validated when the theme requested is not the
    // default, so don't generate it unless necessary.
    // @see \Drupal\Core\Theme\AjaxBasePageNegotiator::determineActiveTheme()
    $active_theme_key = \Drupal::theme()->getActiveTheme()->getName();
    if ($active_theme_key !== \Drupal::service('theme_handler')->getDefault()) {
      $theme_token = \Drupal::csrfToken()->get($active_theme_key);
    }
    else {
      $theme_token = '';
    }

    $request_theme_token = $request->get('ajax_page_state')['theme_token'];

    return $theme_token == $request_theme_token;
  }

  /**
   * {@inheritdoc}
   */
  public function renderResponse(array $main_content, Request $request, RouteMatchInterface $route_match) {
    if (!$this->validatePreconditions($request)) {
      throw new PreconditionFailedHttpException();
    }

    list($page, $title) = $this->htmlRenderer->prepare($main_content, $request, $route_match);

    // Render each region separately and determine whether it has changed.
    $response = new AjaxResponse();
    $regions = \Drupal::theme()->getActiveTheme()->getRegions();
    // Start with page-level HTML <head> attachments and cacheability.
    $metadata = BubbleableMetadata::createFromRenderArray($page);
    foreach ($regions as $region) {
      if (!empty($page[$region])) {
        // @todo Future improvement: only render a region if it is actually
        // going to change. This would yield an even bigger benefit. The benefit
        // today is less data on the wire and particularly fewer things to
        // render in the browser. But we still render everything on the server.
        // This is sufficient for a prototype, but that would yield even better
        // performance.
        $this->renderer->renderRoot($page[$region]);
        $region_metadata = BubbleableMetadata::createFromRenderArray($page[$region]);
        if ($this->refreshlessPageState->hasChanged($region_metadata, $request)) {
          $response->addCommand(new RefreshlessUpdateRegionCommand($region, \Drupal::service('render_cache')->getCacheableRenderArray($page[$region])));
        }

        $metadata = $metadata->merge($region_metadata);
      }
    }

    // Collect all attachments that affect the HTML <head>, render those into
    // HTML and send the appropriate AJAX command. (Note that we do this for
    // all content, including unchanged regions, because we don't know where
    // each tag in the requesting page's <head> bubbled up from, i.e. from which
    // region.)
    $html_head_attachments = array_intersect_key($metadata->getAttachments(), array_flip(static::$htmlHeadAttachmentTypes));
    if (!empty($html_head_attachments)) {
      $response->addCommand(new RefreshlessUpdateHtmlHeadCommand($this->renderTitle($title), $this->renderHtmlHead($html_head_attachments)));
    }

    // Send updated Refreshless page state.
    $response->addAttachments(['drupalSettings' => ['refreshlessPageState' => $this->refreshlessPageState->build($metadata)]]);

    return $response;
  }

  /**
   * Renders HTML <head> attachments into HTML.
   *
   * @param array $html_head_attachments
   *  An attachments array containing only attachments destined for HTML <head>.
   *
   * @return string
   *   The HTML to be inserted in the HTML <head> tag.
   *
   * @see template_preprocess_html()
   * @see \Drupal\Core\Render\HtmlResponseSubscriber
   */
  protected function renderHtmlHead(array $html_head_attachments) {
    assert('empty(array_diff(array_keys($html_head_attachments), static::$htmlHeadAttachmentTypes))', 'Only html_head attachments are passed.');

    $html_attachments = $html_head_attachments;

    // Attachment to render the HTML <head>.
    $placeholder = '<head-placeholder token="' . Crypt::randomBytesBase64(55) . '">';
    $html_attachments['html_response_attachment_placeholders']['head'] = $placeholder;

    // Hardcoded equivalent of core/modules/system/templates/html.html.twig.
    $response = new HtmlResponse();
    $response->setContent($placeholder);
    $response->setAttachments($html_attachments);

    $response = $this->htmlResponseAttachmentsProcessor->processAttachments($response);

    return $response->getContent();
  }

  /**
   * Renders the value for the <title> tag.
   *
   * Unfortunately, the way the title is rendered is entirely dependent on the
   * html.html.twig template of the current theme and the preprocess functions
   * that affect it. So, while we could cover the 90% case by duplicating the
   * title-related parts in template_preprocess_html(), that would not work for
   * themes that either do additional processing, or themes that calculate the
   * title differently.
   * Therefore the only solution is to actually render the html.html.twig
   * template, in way that is as minimal as possible, and then parse it out.
   *
   * @param mixed $title
   *   The page title, as returned by HtmlRenderer::prepare().
   *
   * @return string
   *   The rendered title.
   *
   * @see \Drupal\Core\Render\MainContent\HtmlRenderer::prepare()
   *
   * @todo Simplify when https://www.drupal.org/node/2705293 is fixed.
   */
  protected function renderTitle($title) {
    $minimal_html_to_render_title = [
      '#type' => 'html',
      '#theme' => 'html',
      '#defaults_loaded' => TRUE,
      'page' => ['#title' => $title],
      '#cache' => ['max-age' => 0],
    ];
    $html = (string) $this->renderer->renderPlain($minimal_html_to_render_title);
    $title_start = strpos($html, '<title>') + strlen('<title>');
    $title_stop = strpos($html, '</title>');
    return substr($html, $title_start, $title_stop - $title_start);
  }

}
