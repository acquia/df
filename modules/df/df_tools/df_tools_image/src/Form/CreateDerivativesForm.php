<?php

namespace Drupal\df_tools_image\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

class CreateDerivativesForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'df_tools_image_settings';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['description'] = array(
      '#markup' => '<p>' . t('Image derivatives can be created in bulk manually to remove the need for Drupal to create them on page load.') . '</p>',
    );
    $form['run'] = array(
      '#type' => 'submit',
      '#value' => t('Create derivatives'),
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    return df_tools_image_seed_derivatives();
  }
}

