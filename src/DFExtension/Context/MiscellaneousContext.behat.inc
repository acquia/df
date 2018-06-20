<?php

namespace Acquia\DFExtension\Context;

use Drupal\DrupalExtension\Context\DrupalSubContextBase;

/**
 * A context with miscellaneous helpers.
 */
class MiscellaneousContext extends DrupalSubContextBase {

  /**
   * Asserts that the current page is an admin page.
   *
   * @Then The page should be an admin page
   */
  public function assertAdminPage() {
    /** @var \Drupal\Core\Routing\AdminContext $admin_context */
    $admin_context = \Drupal::service('router.admin_context');
    return $admin_context->isAdminRoute();
  }

}
