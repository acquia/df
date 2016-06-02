<?php

/**
 * @file
 * Contains Drupal\scheduler\Exception\SchedulerNodeTypeNotEnabledException.
 */

namespace Drupal\scheduler\Exception;

/**
 * Defines an exception thrown when Scheduler attempts to publish or unpublish
 * a node during cron but the node type is not enabled for Scheduler.
 *
 * @see \Drupal\scheduler\SchedulerManager::publish()
 * @see \Drupal\scheduler\SchedulerManager::unpublish()
 */
class SchedulerNodeTypeNotEnabledException extends \Exception { }
