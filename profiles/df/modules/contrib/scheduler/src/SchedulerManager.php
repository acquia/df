<?php

/**
 * @file
 * Contains \Drupal\scheduler\SchedulerManager.
 */

namespace Drupal\scheduler;

use Drupal\Core\Config\ConfigFactory;
use Drupal\Core\Datetime\DateFormatter;
use Drupal\Core\Entity\EntityManager;
use Drupal\Core\Url;
use Drupal\Core\Extension\ModuleHandler;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;
use Drupal\scheduler\Exception\SchedulerMissingDateException;
use Drupal\scheduler\Exception\SchedulerNodeTypeNotEnabledException;
use Psr\Log\LoggerInterface;

/**
 * Defines a scheduler manager.
 */
class SchedulerManager {

  /**
   * Date formatter service object.
   *
   * @var \Drupal\Core\Datetime\DateFormatter
   */
  protected $dateFormatter;

  /**
   * Scheduler Logger service object.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * Module handler service object.
   *
   * @var  \Drupal\Core\Extension\ModuleHandler
   */
  protected $moduleHandler;

  /**
   * Entity Manager service object.
   *
   * @var  \Drupal\Core\Entity\EntityManager
   */
  protected $entityManager;

  /**
   * Config Factory service object.
   *
   * @var \Drupal\Core\Config\ConfigFactory
   */
  protected $configFactory;

  /**
   * Constructs a SchedulerManager object.
   */
  public function __construct(DateFormatter $dateFormatter, LoggerInterface $logger, ModuleHandler $moduleHandler, EntityManager $entityManager, ConfigFactory $configFactory) {
    $this->dateFormatter = $dateFormatter;
    $this->logger = $logger;
    $this->moduleHandler = $moduleHandler;
    $this->entityManager = $entityManager;
    $this->configFactory = $configFactory;
  }

  /**
   * Publish scheduled nodes.
   *
   * @return bool
   *   TRUE if any node has been published, FALSE otherwise.
   *
   * @throws \Drupal\scheduler\Exception\SchedulerMissingDateException
   * @throws \Drupal\scheduler\Exception\SchedulerNodeTypeNotEnabledException
   */
  public function publish() {
    /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher */
    $dispatcher = \Drupal::service('event_dispatcher');

    $result = FALSE;

    // If the time now is greater than the time to publish a node, publish it.
    $query = \Drupal::entityQuery('node')
      ->condition('publish_on', 0, '>')
      ->condition('publish_on', REQUEST_TIME, '<=');
    // @todo Change this query to exclude nodes which are not enabled for
    // publishing. See https://www.drupal.org/node/2659824
    $nids = $query->execute();

    $action = 'publish';

    // Allow other modules to add to the list of nodes to be published.
    $nids = array_unique(array_merge($nids, $this->nidList($action)));

    // Allow other modules to alter the list of nodes to be published.
    $this->moduleHandler->alter('scheduler_nid_list', $nids, $action);

    $nodes = Node::loadMultiple($nids);
    foreach ($nodes as $nid => $node) {
      // The API calls could return nodes of types which are not enabled for
      // scheduled publishing. Do not process these.
      if (!$node->type->entity->getThirdPartySetting('scheduler', 'publish_enable', SCHEDULER_DEFAULT_PUBLISH_ENABLE)) {
        throw new SchedulerNodeTypeNotEnabledException(sprintf("Node %d '%s' will not be published because node type '%s' is not enabled for scheduled publishing", $node->id(), $node->getTitle(), node_get_type_label($node)));
        continue;
      }

      // Check that other modules allow the action on this node.
      if (!$this->isAllowed($node, $action)) {
        continue;
      }

      // Trigger the PRE_PUBLISH event so that modules can react before the node
      // is published.
      $event = new SchedulerEvent($node);
      $dispatcher->dispatch(SchedulerEvents::PRE_PUBLISH, $event);
      $node = $event->getNode();

      // If an API call has removed the date $node->set('changed', $publish_on)
      // would fail, so trap this exception here and give a meaningful message.
      if (empty($node->publish_on->value)) {
        $field_definitions = $this->entityManager->getFieldDefinitions('node', $node->getType());
        $field = (string)$field_definitions['publish_on']->getLabel();
        throw new SchedulerMissingDateException(sprintf("Node %d '%s' will not be published because field '%s' has no value", $node->id(), $node->getTitle(), $field));
        continue;
      }

      // Update timestamps.
      $publish_on = $node->publish_on->value;
      $node->set('changed', $publish_on);
      $old_creation_date = $node->getCreatedTime();
      if ($node->type->entity->getThirdPartySetting('scheduler', 'publish_touch', SCHEDULER_DEFAULT_PUBLISH_TOUCH)) {
        $node->setCreatedTime($publish_on);
      }

      $create_publishing_revision = $node->type->entity->getThirdPartySetting('scheduler', 'publish_revision', SCHEDULER_DEFAULT_PUBLISH_REVISION);
      if ($create_publishing_revision) {
        $node->setNewRevision();
        // Use a core date format to guarantee a time is included.
        $node->revision_log = t('Node published by Scheduler on @now. Previous creation date was @date.', array(
          '@now' => $this->dateFormatter->format(REQUEST_TIME, 'short'),
          '@date' => $this->dateFormatter->format($old_creation_date, 'short'),
        ));
      }
      // Unset publish_on so the node will not get rescheduled by subsequent
      // calls to $node->save().
      $node->publish_on->value = NULL;

      // Log the fact that a scheduled publication is about to take place.
      $view_link = $node->link(t('View node'));
      $nodetype_url = Url::fromRoute('entity.node_type.edit_form', array('node_type' => $node->getType()));
      $nodetype_link = \Drupal::l(node_get_type_label($node) . ' ' . t('settings'), $nodetype_url);
      $logger_variables = array(
        '@type' => node_get_type_label($node),
        '%title' => $node->getTitle(),
        'link' => $nodetype_link . ' ' . $view_link,
      );
      $this->logger->notice('@type: scheduled publishing of %title.', $logger_variables);

      // Use the actions system to publish the node.
      $this->entityManager->getStorage('action')->load('node_publish_action')->getPlugin()->execute($node);

      // Invoke the event to tell Rules that Scheduler has published this node.
      if ($this->moduleHandler->moduleExists('rules')) {
        /*
        TEMP remove call to undefined function rules_invoke_event until converted.
        @see https://www.drupal.org/node/2651348
        rules_invoke_event('scheduler_node_has_been_published_event', $node, $publish_on, $node->unpublish_on->value);
        */
      }

      // Trigger the PUBLISH event so that modules can react after the node is
      // published.
      $event = new SchedulerEvent($node);
      $dispatcher->dispatch(SchedulerEvents::PUBLISH, $event);
      $event->getNode()->save();

      $result = TRUE;
    }

    return $result;
  }

  /**
   * Unpublish scheduled nodes.
   *
   * @return bool
   *   TRUE if any node has been unpublished, FALSE otherwise.
   *
   * @throws \Drupal\scheduler\Exception\SchedulerMissingDateException
   * @throws \Drupal\scheduler\Exception\SchedulerNodeTypeNotEnabledException
   */
  public function unpublish() {
    /** @var \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher */
    $dispatcher =  \Drupal::service('event_dispatcher');

    $result = FALSE;

    // If the time is greater than the time to unpublish a node, unpublish it.
    $query = \Drupal::entityQuery('node')
      ->condition('unpublish_on', 0, '>')
      ->condition('unpublish_on', REQUEST_TIME, '<=');
    // @todo Change this query to exclude nodes which are not enabled for
    // unpublishing. See https://www.drupal.org/node/2659824
    $nids = $query->execute();

    $action = 'unpublish';

    // Allow other modules to add to the list of nodes to be unpublished.
    $nids = array_unique(array_merge($nids, $this->nidList($action)));

    // Allow other modules to alter the list of nodes to be unpublished.
    $this->moduleHandler->alter('scheduler_nid_list', $nids, $action);

    $nodes = Node::loadMultiple($nids);
    foreach ($nodes as $nid => $node) {
      // The API calls could return nodes of types which are not enabled for
      // scheduled unpublishing. Do not process these.
      if (!$node->type->entity->getThirdPartySetting('scheduler', 'unpublish_enable', SCHEDULER_DEFAULT_UNPUBLISH_ENABLE)) {
        throw new SchedulerNodeTypeNotEnabledException(sprintf("Node %d '%s' will not be unpublished because node type '%s' is not enabled for scheduled unpublishing", $node->id(), $node->getTitle(), node_get_type_label($node)));
        continue;
      }

      // Check that other modules allow the action on this node.
      if (!$this->isAllowed($node, $action)) {
        continue;
      }

      // Do not process the node if it still has a publish_on time which is in
      // the past, as this implies that scheduled publishing has been blocked by
      // one of the hook functions we provide, and is still being blocked now
      // that the unpublishing time has been reached.
      $publish_on = $node->publish_on->value;
      if (!empty($publish_on) && $publish_on <= REQUEST_TIME) {
        continue;
      }

      // Trigger the PRE_UNPUBLISH event so that modules can react before the
      // node is unpublished.
      $event = new SchedulerEvent($node);
      $dispatcher->dispatch(SchedulerEvents::PRE_UNPUBLISH, $event);
      $node = $event->getNode();

      // If an API call has removed the date $node->set('changed', $unpublish_on)
      // would fail, so trap this exception here and give a meaningful message.
      if (empty($node->unpublish_on->value)) {
        $field_definitions = $this->entityManager->getFieldDefinitions('node', $node->getType());
        $field = (string)$field_definitions['unpublish_on']->getLabel();
        throw new SchedulerMissingDateException(sprintf("Node %d '%s' will not be unpublished because field '%s' has no value", $node->id(), $node->getTitle(), $field));
        continue;
      }

      // Update timestamps.
      $old_change_date = $node->getChangedTime();
      $unpublish_on = $node->unpublish_on->value;
      $node->set('changed', $unpublish_on);

      $create_unpublishing_revision = $node->type->entity->getThirdPartySetting('scheduler', 'unpublish_revision', SCHEDULER_DEFAULT_UNPUBLISH_REVISION);
      if ($create_unpublishing_revision) {
        $node->setNewRevision();
        // Use a core date format to guarantee a time is included.
        $node->revision_log = t('Node unpublished by Scheduler on @now. Previous change date was @date.', array(
          '@now' => $this->dateFormatter->format(REQUEST_TIME, 'short'),
          '@date' => $this->dateFormatter->format($old_change_date, 'short'),
        ));
      }
      // Unset unpublish_on so the node will not get rescheduled by subsequent
      // calls to $node->save(). Save the value for use when calling Rules.
      $node->unpublish_on->value = NULL;

      // Log the fact that a scheduled unpublication is about to take place.
      $view_link = $node->link(t('View node'));
      $nodetype_url = Url::fromRoute('entity.node_type.edit_form', array('node_type' => $node->getType()));
      $nodetype_link = \Drupal::l(node_get_type_label($node) . ' ' . t('settings'), $nodetype_url);
      $logger_variables = array(
        '@type' => node_get_type_label($node),
        '%title' => $node->getTitle(),
        'link' => $nodetype_link . ' ' . $view_link,
      );
      $this->logger->notice('@type: scheduled unpublishing of %title.', $logger_variables);

      // Use the actions system to publish the node.
      $this->entityManager->getStorage('action')->load('node_unpublish_action')->getPlugin()->execute($node);

      // Invoke event to tell Rules that Scheduler has unpublished this node.
      if ($this->moduleHandler->moduleExists('rules')) {
        /*
        TEMP remove call to undefined function rules_invoke_event until converted.
        @see https://www.drupal.org/node/2651348
        rules_invoke_event('scheduler_node_has_been_unpublished_event', $node, $node->publish_on, $unpublish_on);
        */
      }

      // Trigger the UNPUBLISH event so that modules can react before the node
      // is unpublished.
      $event = new SchedulerEvent($node);
      $dispatcher->dispatch(SchedulerEvents::UNPUBLISH, $event);
      $event->getNode()->save();

      $result = TRUE;
    }

    return $result;
  }

  /**
   * Checks whether a scheduled action on a node is allowed.
   *
   * This provides a way for other modules to prevent scheduled publishing or
   * unpublishing, by implementing hook_scheduler_allow_publishing() or
   * hook_scheduler_allow_unpublishing().
   *
   * @see hook_scheduler_allow_publishing()
   * @see hook_scheduler_allow_unpublishing()
   *
   * @param \Drupal\node\NodeInterface $node
   *   The node on which the action is to be performed.
   * @param string $action
   *   The action that needs to be checked. Can be 'publish' or 'unpublish'.
   *
   * @return bool
   *   TRUE if the action is allowed, FALSE if not.
   */
  public function isAllowed(NodeInterface $node, $action) {
    // Default to TRUE.
    $result = TRUE;
    // Check that other modules allow the action.
    $hook = 'scheduler_allow_' . $action . 'ing';
    foreach ($this->moduleHandler->getImplementations($hook) as $module) {
      $function = $module . '_' . $hook;
      $result &= $function($node);
    }

    return $result;
  }

  /**
   * Gather node IDs for all nodes that need to be $action'ed.
   *
   * @param string $action
   *   The action being performed, either "publish" or "unpublish".
   *
   * @return array
   *   An array of node ids.
   */
  public function nidList($action) {
    $nids = array();

    foreach ($this->moduleHandler->getImplementations('scheduler_nid_list') as $module) {
      $function = $module . '_scheduler_nid_list';
      $nids = array_merge($nids, $function($action));
    }

    return $nids;
  }

  /**
   * Run the lightweight cron.
   *
   * The Scheduler part of the processing performed here is the same as in the
   * normal Drupal cron run. The difference is that only scheduler_cron() is
   * executed, no other modules hook_cron() functions are called.
   *
   * This function is called from the external crontab job via url
   * /scheduler/cron/{access key} or it can be run interactively from the
   * Scheduler configuration page at /admin/config/content/scheduler/cron.
   */
  public function runCron() {
    $log = $this->configFactory->get('scheduler.settings')->get('log');
    if ($log) {
      $this->logger->notice('Lightweight cron run activated.');
    }
    scheduler_cron();
    if (ob_get_level() > 0) {
      $handlers = ob_list_handlers();
      if (isset($handlers[0]) && $handlers[0] == 'default output handler') {
        ob_clean();
      }
    }
    if ($log) {
      $this->logger->notice('Lightweight cron run completed.', array('link' => \Drupal::l(t('settings'), Url::fromRoute('scheduler.cron_form'))));
    }
  }

}
