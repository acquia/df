<?php

/**
 * @file
 * Contains \Drupal\scheduler\SchedulerEvent.
 */

namespace Drupal\scheduler;

use Drupal\Core\Entity\EntityInterface;
use Symfony\Component\EventDispatcher\Event;

/**
 * Wraps a scheduler event for event listeners.
 */
class SchedulerEvent extends Event {

  /**
   * Node object.
   *
   * @var EntityInterface $node
   */
  protected $node;

  /**
   * Constructs a scheduler event object.
   *
   * @param \Drupal\Core\Entity\EntityInterface
   *   Node object.
   */
  public function __construct(EntityInterface $node) {
    $this->node = $node;
  }

  /**
   * Gets node object.
   *
   * @return \Drupal\Core\Entity\EntityInterface
   *   The node object that caused the event to fire.
   */
  public function getNode() {
    return $this->node;
  }

  /**
   * Sets the node object.
   *
   * @param \Drupal\Core\Entity\EntityInterface $node
   */
  public function setNode(EntityInterface $node) {
    $this->node = $node;
  }

}
