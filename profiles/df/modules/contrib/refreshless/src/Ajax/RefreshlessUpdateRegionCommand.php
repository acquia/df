<?php

/**
 * @file
 * Contains \Drupal\refreshless\Ajax\RefreshlessUpdateRegionCommand.
 */

namespace Drupal\refreshless\Ajax;

use Drupal\Core\Ajax\CommandInterface;
use Drupal\Core\Ajax\CommandWithAttachedAssetsInterface;
use Drupal\Core\Ajax\CommandWithAttachedAssetsTrait;

/**
 * AJAX command for updating a region in the page.
 *
 * This command is implemented by Drupal.AjaxCommands.prototype.refreshlessUpdateRegion()
 * defined in js/refreshless.js
 *
 * @ingroup ajax
 *
 * @see \Drupal\Core\Ajax\InsertCommand
 */
class RefreshlessUpdateRegionCommand implements CommandInterface, CommandWithAttachedAssetsInterface {

  use CommandWithAttachedAssetsTrait;

  /**
   * A region name.
   *
   * @var string
   */
  protected $region;

  /**
   * The render array for the region.
   *
   * @var array
   */
  protected $content;

  /**
   * Constructs an RefreshlessUpdateRegionCommand object.
   *
   * @param string $region
   *   A region name.
   * @param array $content
   *   The render array with the content for the region.
   */
  public function __construct($region, array $content) {
    assert('is_string($region)');
    $this->region = $region;
    $this->content = $content;
  }

  /**
   * {@inheritdoc}
   */
  public function render() {
    return [
      'command' => 'refreshlessUpdateRegion',
      'region' => $this->region,
      'data' => $this->getRenderedContent(),
    ];
  }

}
