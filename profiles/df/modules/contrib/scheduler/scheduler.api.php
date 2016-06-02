<?php

/**
 * @file
 * API documentation for the Scheduler module.
 */

/**
 * @addtogroup hooks
 * @{
 */

/**
 * Modules can implement hook_scheduler_nid_list() to add more node ids into the
 * list to be processed in the current cron run. This hook is invoked during
 * cron runs only.
 *
 * @param string $action
 *   $action determines what is being done to the node.
 *   The value will be 'publish' or 'unpublish'.
 *
 * @return array
 *   Array of node ids to add to the existing list of nodes to be processed.
 */
function hook_scheduler_nid_list($action) {
  $nids = array();
  // Do some processing to add new node ids and dates.
  return $nids;
}

/**
 * Modules can implement hook_scheduler_nid_list_alter() to add or remove node
 * ids from the list to be processed in the current cron run. This hook is
 * invoked during cron runs only.
 *
 * @param array $nids
 *   $nids is an array of node ids being processed.
 *
 * @param string $action
 *   $action determines what is being done to the node.
 *   The value will be 'publish' or 'unpublish'.
 *
 * @return array
 *   The full array of node ids to process, adjusted as required.
 */
function hook_scheduler_nid_list_alter(&$nids, $action) {
  return $nids;
}

/**
 * Modules can implement hook_scheduler_allow_publishing() to prevent
 * publication of a scheduled node.
 *
 * The node can be scheduled, and an attempt to publish it will be made during
 * the first cron run after the publishing time. If this hook returns FALSE the
 * node will not be published. Attempts at publishing will continue on each
 * subsequent cron run until this hook returns TRUE.
 *
 * @param \Drupal\node\NodeInterface $node
 *   The scheduled node that is about to be published.
 *
 * @return bool
 *   TRUE if the node can be published, FALSE if it should not be published.
 */
function hook_scheduler_allow_publishing(NodeInterface $node) {
  // If there is no 'approved' field do nothing to change the result.
  if (!isset($node->field_approved)) {
    $allowed = TRUE;
  }
  else {
    // Prevent publication of nodes that do not have the 'Approved for
    // publication by the CEO' checkbox ticked.
    $allowed = !empty($node->field_approved->value);

    // If publication is denied then inform the user why. This message will be
    // displayed during node edit and save.
    if (!$allowed) {
      drupal_set_message(t('The content will only be published after approval by the CEO.'), 'status', FALSE);
    }
  }

  return $allowed;
}

/**
 * Modules can implement hook_scheduler_allow_unpublishing() to prevent
 * unpublication of a scheduled node.
 *
 * The node can be scheduled, and an attempt to unpublish it will be made during
 * the first cron run after the unpublishing time. If this hook returns FALSE
 * the node will not be unpublished. Attempts at unpublishing will continue on
 * each subsequent cron run until this hook returns TRUE.
 *
 * @param \Drupal\node\NodeInterface $node
 *   The scheduled node that is about to be unpublished.
 *
 * @return bool
 *   TRUE if the node can be unpublished, FALSE if it should not be unpublished.
 */
function hook_scheduler_allow_unpublishing(NodeInterface $node) {
  $allowed = TRUE;

  // Prevent unpublication of competition entries if not all prizes have been
  // claimed.
  if ($node->getType() == 'competition' && $items = $node->field_competition_prizes->getValue()) {
    $allowed = (bool) count($items);

    // If unpublication is denied then inform the user why. This message will be
    // displayed during node edit and save.
    if (!$allowed) {
      drupal_set_message(t('The competition will only be unpublished after all prizes have been claimed by the winners.'));
    }
  }

  return $allowed;
}

/**
 * @} End of "addtogroup hooks".
 */
