<?php

/**
 * @file
 * Contains \Drupal\addtoany\Form\AddToAnySettingsForm.
 */

namespace Drupal\addtoany\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure AddToAny settings for this site.
 */
class AddToAnySettingsForm extends ConfigFormBase {
  /**
   * {@inheritdoc}
   */
  public function getFormID() {
    return 'addtoany_settings_form';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return [
      'addtoany.settings',
    ];
  }

  public function buildForm(array $form, FormStateInterface $form_state) {

    global $base_path;

    $addtoany_settings = $this->config('addtoany.settings');

    $button_img = '<img src="' . $base_path . drupal_get_path('module', 'addtoany') . '/images/%s" width="%d" height="%d"%s />';

    $button_options = array(
      'default' => sprintf($button_img, 'a2a_32_32.svg', 32, 32, ' class="addtoany-round-icon"'),
      'custom' => t('Custom button'),
      'none' => t('None'),
    );

    $attributes_for_code = array(
      'autocapitalize' => array('off'),
      'autocomplete' => array('off'),
      'autocorrect' => array('off'),
      'spellcheck' => array('false'),
    );

    // Attach CSS and JS
    $form['#attached']['library'][] = 'addtoany/addtoany.admin';

    $form['addtoany_button_settings'] = array(
      '#type'         => 'details',
      '#title'        => t('Buttons'),
      '#open'         => TRUE,
    );
    $form['addtoany_button_settings']['addtoany_buttons_size'] = array(
      '#type'          => 'number',
      '#title'         => t('Icon size'),
      '#field_suffix'  => ' ' . t('pixels'),
      '#default_value' => $addtoany_settings->get('buttons_size'),
      '#size'          => 10,
      '#maxlength'     => 3,
      '#min'           => 8, // Replaces D7's element_validate_integer_positive() validation
      '#max'           => 999,
      '#required'      => TRUE,
    );
    $form['addtoany_button_settings']['addtoany_service_button_settings'] = array(
      '#type'         => 'details',
      '#title'        => t('Service Buttons'),
      '#collapsible'  => TRUE,
      '#collapsed'    => TRUE,
    );
    $form['addtoany_button_settings']['addtoany_service_button_settings']['addtoany_additional_html'] = array(
      '#type'          => 'textarea',
      '#title'         => t('Service Buttons HTML code'),
      '#default_value' => $addtoany_settings->get('additional_html'),
      '#description'   => t('You can add HTML code to display customized <a href="https://www.addtoany.com/buttons/customize/standalone_services" target="_blank">standalone service buttons</a> next to each universal share button. For example: <br /> <code>&lt;a class=&quot;a2a_button_facebook&quot;&gt;&lt;/a&gt;<br />&lt;a class=&quot;a2a_button_twitter&quot;&gt;&lt;/a&gt;<br />&lt;a class=&quot;a2a_button_pinterest&quot;&gt;&lt;/a&gt;</code>
      '),
      '#attributes' => $attributes_for_code,
    );
    $form['addtoany_button_settings']['universal_button'] = array(
      '#type'         => 'details',
      '#title'        => t('Universal Button'),
      '#collapsible'  => TRUE,
      '#collapsed'    => TRUE,
      /* #states workaround in addtoany.admin.js */
    );
    $form['addtoany_button_settings']['universal_button']['addtoany_universal_button'] = array(
      '#type'          => 'radios',
      '#title'         => t('Button'),
      '#default_value' => $addtoany_settings->get('universal_button'),
      '#attributes'    => array('class' => array('addtoany-universal-button-option')),
      '#options'       => $button_options,
    );
    $form['addtoany_button_settings']['universal_button']['addtoany_custom_universal_button'] = array(
      '#type'          => 'textfield',
      '#title'         => t('Custom button URL'),
      '#default_value' => $addtoany_settings->get('custom_universal_button'),
      '#description'   => t('URL of the button image. Example: http://example.com/share.png'),
      '#states'        => array(
        // Show only if custom button is selected
        'visible' => array(
          ':input[name="addtoany_universal_button"]' => array('value' => 'custom'),
        ),
      ),
    );
    $form['addtoany_button_settings']['universal_button']['addtoany_universal_button_placement'] = array(
      '#type'          => 'radios',
      '#title'         => t('Button placement'),
      '#default_value' => $addtoany_settings->get('universal_button_placement'),
      '#options'       => array(
        'after' => t('After the service buttons'),
        'before' => t('Before the service buttons'),
      ),
      '#states'        => array(
        // Hide when universal sharing is disabled
        'invisible' => array(
          ':input[name="addtoany_universal_button"]' => array('value' => 'none'),
        ),
      ),
    );

    $form['addtoany_placement_settings'] = array(
      '#type'  => 'details',
      '#title' => t('Placement'),
      '#collapsible'  => TRUE,
      '#collapsed'    => TRUE,
    );
    $form['addtoany_placement_settings']['addtoany_nodetypes'] = array(
      '#type'          => 'checkboxes',
      '#title'         => t('Node types'),
      '#description'   => t('Display buttons for these node types.'),
      '#default_value' => $addtoany_settings->get('nodetypes'),
      '#options'       => node_type_get_names(),
    );
    $form['addtoany_placement_settings']['addtoany_display_in_teasers'] = array(
      '#type'          => 'checkbox',
      '#title'         => t('Display for node teasers'),
      '#default_value' => $addtoany_settings->get('display_in_teasers'),
      '#description'   => t('Display buttons for node teasers in selected sections.'),
      '#states'        => array(
        // Disable if no section placement is selected
        'disabled' => array(
          ':input[name="addtoany_display_in_nodecont"]' => array('checked' => FALSE),
        ),
      ),
    );
    $form['addtoany_placement_settings']['addtoany_display_in_nodecont'] = array(
      '#type'          => 'checkbox',
      '#title'         => t('Display in content section'),
      '#default_value' => $addtoany_settings->get('display_in_nodecont'),
      '#description'   => t('Display buttons in the content section of node pages.'),
    );
    $form['addtoany_placement_settings']['addtoany_display_weight'] = array(
      '#type'          => 'weight',
      '#title'         => t('Content weight'),
      '#default_value' => $addtoany_settings->get('display_weight'),
      '#delta'         => 50,
      '#description'   => t('Optional weight value for reordering AddToAny within the content section.'),
      '#states'        => array(
        // Show only if placement in "content" selected
        'visible' => array(
          ':input[name="addtoany_display_in_nodecont"]' => array('checked' => TRUE),
        ),
      ),
    );

    $form['addtoany_additional_settings'] = array(
      '#type'         => 'details',
      '#title'        => t('Additional options'),
      '#collapsible'  => TRUE,
      '#collapsed'    => TRUE,
    );
    $form['addtoany_additional_settings']['addtoany_additional_js'] = array(
      '#type'          => 'textarea',
      '#title'         => t('Additional JavaScript'),
      '#default_value' => $addtoany_settings->get('additional_js'),
      '#description'   => t('You can add special JavaScript code for AddToAny. See <a href="https://www.addtoany.com/buttons/customize/drupal" target="_blank">AddToAny documentation</a>.'),
      '#attributes' => $attributes_for_code,
    );
    $form['addtoany_additional_settings']['addtoany_additional_css'] = array(
      '#type'          => 'textarea',
      '#title'         => t('Additional CSS'),
      '#default_value' => $addtoany_settings->get('additional_css'),
      '#description'   => t('You can add special CSS code for AddToAny. See <a href="https://www.addtoany.com/buttons/customize/drupal" target="_blank">AddToAny documentation</a>.'),
      '#attributes' => $attributes_for_code,
    );
    $form['addtoany_additional_settings']['addtoany_no_3p'] = array(
      '#type'          => 'checkbox',
      '#title'         => t('Disable 3rd party cookies'),
      '#default_value' => $addtoany_settings->get('no_3p'),
      '#description'   => t('Disabling may affect analytics and limit some functionality.'),
    );

    return parent::buildForm($form, $form_state);
  }

  public function submitForm(array &$form, FormStateInterface $form_state) {
    $values = $form_state->getValues();
    $this->config('addtoany.settings')
      ->set('additional_css', $values['addtoany_additional_css'])
      ->set('additional_html', $values['addtoany_additional_html'])
      ->set('additional_js', $values['addtoany_additional_js'])
      ->set('buttons_size', $values['addtoany_buttons_size'])
      ->set('custom_universal_button', $values['addtoany_custom_universal_button'])
      ->set('display_in_nodecont', $values['addtoany_display_in_nodecont'])
      ->set('display_in_teasers', $values['addtoany_display_in_teasers'])
      ->set('display_weight', $values['addtoany_display_weight'])
      ->set('universal_button', $values['addtoany_universal_button'])
      ->set('universal_button_placement', $values['addtoany_universal_button_placement'])
      ->set('nodetypes', array_values(array_filter($values['addtoany_nodetypes'])))
      ->set('no_3p', $values['addtoany_no_3p'])
      ->save();

    parent::submitForm($form, $form_state);
  }

}
