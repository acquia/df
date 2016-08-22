<?php

namespace Drupal\df_tools_blocks;

use Drupal\replicate\Replicator;

/**
 * Class Copier.
 *
 * @package Drupal\df_tools_blocks
 */
class Copier {

  /**
   * Drupal\replicate\Replicator definition.
   *
   * @var \Drupal\replicate\Replicator
   */
  protected $replicator;

  /**
   * Constructor.
   */
  public function __construct(Replicator $replicator) {
    $this->replicator = $replicator;
  }

  public function makeCopy($entity, $options = null) {
    // Copy and return the entity.
    if ($copy = $this->replicator->replicateEntity($entity)) {
      return $copy;
    }
    else {
      return false;
    }
  }

}
