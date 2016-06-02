<?php

/**
 * @file
 * Contains \Drupal\scheduler\Plugin\Validation\Constraint\SchedulerPublishOnConstraintValidator.
 */

namespace Drupal\scheduler\Plugin\Validation\Constraint;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Validates the SchedulerPublishOn constraint.
 */
class SchedulerPublishOnConstraintValidator extends ConstraintValidator {

  /**
   * {@inheritdoc}
   */
  public function validate($entity, Constraint $constraint) {
    $publish_on = $entity->value;
    $scheduler_publish_past_date = $entity->getEntity()->type->entity->getThirdPartySetting('scheduler', 'publish_past_date', SCHEDULER_DEFAULT_PUBLISH_PAST_DATE);

    if ($publish_on && $scheduler_publish_past_date == 'error' && $publish_on < REQUEST_TIME) {
      $this->context->buildViolation($constraint->messagePublishOnDateNotInFuture)
        ->atPath('publish_on')
        ->addViolation();
    }
  }
}
