<?php

/**
 * @file
 * Contains \Drupal\refreshless\Ajax\RefreshlessUpdateHtmlHeadCommand.
 */

namespace Drupal\refreshless\Ajax;

use Drupal\Core\Ajax\CommandInterface;
use Drupal\Core\Ajax\CommandWithAttachedAssetsTrait;

/**
 * AJAX command for updating the HTML <head>.
 *
 * Note that this does not touch CSS or JS in <head>: for those, the AJAX system
 * has separate commands, we don't need to do anything about that. This is for
 * everything in <head> *except* CSS and JS.
 * @see \Drupal\Core\Ajax\AjaxResponseAttachmentsProcessor::buildAttachmentsCommands()
 *
 * This command is implemented by Drupal.AjaxCommands.prototype.refreshlessUpdateHtmlHead()
 * defined in js/refreshless.js
 *
 * @ingroup ajax
 */
class RefreshlessUpdateHtmlHeadCommand implements CommandInterface {

  /**
   * The page title, to be set as <title> in the HTML <head>.
   *
   * @var string
   */
  protected $title;

  /**
   * The HTML <head> markup.
   *
   * @var string
   */
  protected $headMarkup;

  /**
   * Constructs an RefreshlessUpdateRegionCommand object.
   *
   * @param string $title
   *   The page title, to be set as <title> in the HTML <head>.
   * @param string $head_markup
   *   The HTML <head> markup.
   */
  public function __construct($title, $head_markup) {
    assert('is_string($title)');
    assert('is_string($head_markup)');
    $this->title = $title;
    $this->headMarkup = $head_markup;
  }

  /**
   * {@inheritdoc}
   */
  public function render() {
    return [
      'command' => 'refreshlessUpdateHtmlHead',
      'title' => $this->title,
      'headMarkup' => $this->headMarkup,
    ];
  }

}
