<?php

/**
 * @file
 * Contains Drupal\scheduler\Exception\SchedulerMissingDateException.
 */

namespace Drupal\scheduler\Exception;

/**
 * Defines an exception thrown when Scheduler attempts to publish or unpublish
 * a node during cron but the date is missing.
 *
 * @see \Drupal\scheduler\SchedulerManager::publish()
 * @see \Drupal\scheduler\SchedulerManager::unpublish()
 */
class SchedulerMissingDateException extends \Exception { }
