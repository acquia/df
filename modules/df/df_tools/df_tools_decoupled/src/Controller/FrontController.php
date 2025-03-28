<?php

namespace Drupal\df_tools_decoupled\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\user\Form\UserLoginForm;

class FrontController extends ControllerBase {

  /**
   * Displays the login form on the homepage and redirects authenticated users.
   */
  public function frontpage() {
    $build = [];
    if ($this->currentUser()->isAnonymous()) {
      $build['form'] = $this->formBuilder()->getForm(UserLoginForm::class);
    }
    else {
      if (\Drupal::moduleHandler()->moduleExists('moderation_dashboard')
        && $this->currentUser()->hasPermission('use moderation dashboard')) {
          // Permitted users are directed to their moderation dashboard.
          return $this->redirect('view.moderation_dashboard.page_1', ['user' => $this->currentUser()->id()]);
      }
      elseif ($this->currentUser()->hasPermission('access content overview')) {
        // Permitted users are directed to the admin content page.
        return $this->redirect('view.content.page_1');
      }
      else {
        $build['heading'] = [
          '#type' => 'markup',
          '#markup' => $this->t('This site has no homepage content.'),
        ];
      }
    }
    return $build;
  }
}
