<?php

/**
 * @file
 * Contains \Drupal\geocoder_field\Plugin\Field\FieldWidget\GeocodeWidgetBase.
 */

namespace Drupal\geocoder_field\Plugin\Field;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\geocoder\Geocoder;
use Symfony\Component\Validator\ConstraintViolationInterface;

/**
 * Base Geocode Widget implementation for the Geocoder Field module.
 */
abstract class GeocodeWidgetBase extends WidgetBase {
  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return array(
      'field' => '',
      'provider_plugins' => array(),
      'dumper_plugin' => 'wkt',
      'delta_handling' => 'default',
    ) + parent::defaultSettings();
  }

  /**
   * Get the list of enabled Provider plugins.
   *
   * @return array
   */
  public function getEnabledProviderPlugins() {
    $provider_plugin_ids = array();
    $geocoder_plugins = Geocoder::getPlugins('Provider');

    foreach ($this->getSetting('provider_plugins') as $plugin_id => $plugin) {
      if ($plugin['checked']) {
        $provider_plugin_ids[$plugin_id] = $geocoder_plugins[$plugin_id];
      }
    }

    return $provider_plugin_ids;
  }

  /**
   * Get the list of options for the delta handling select box.
   *
   * @return array
   */
  public function getDeltaHandling() {
    return array(
      'default' => $this->t('Match Multiples (default)'),
      'm_to_s' => $this->t('Multiple to Single'),
      's_to_m' => $this->t('Single to Multiple'),
      'c_to_s' => $this->t('Concatenate to Single'),
      'c_to_m' => $this->t('Concatenate to Multiple'),
    );
  }

  /**
   * Get the list of available fields.
   *
   * @return array
   */
  public function getAvailableFields() {
    $types = array();
    $options = array();
    /** @var \Drupal\Core\Entity\EntityFieldManagerInterface $entity_field_manager */
    $entity_field_manager = \Drupal::service('entity_field.manager');

    $definitions = \Drupal::service('plugin.manager.geocoder.data_prepare')->getDefinitions();
    foreach ($definitions as $definition) {
      foreach ($definition['field_types'] as $field_type) {
        $types[$field_type] = $field_type;
      }
    }

    $entity_field_definitions = $entity_field_manager->getFieldDefinitions($this->fieldDefinition->getTargetEntityTypeId(), $this->fieldDefinition->getTargetBundle());
    foreach ($entity_field_definitions as $id => $definition) {
      if (in_array($definition->getType(), array_values($types)) && ($definition->getName() != $this->fieldDefinition->getName())) {
        $options[$id] = sprintf('%s (%s)(%s)', $definition->getLabel(), $definition->getName(), $definition->getType());
      }
    }

    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements = parent::settingsForm($form, $form_state);

    $elements['field'] = array(
      '#type' => 'select',
      '#weight' => 10,
      '#title' => $this->t('Field to use'),
      '#description' => $this->t('Select which field you would like to use.'),
      '#default_value' => $this->getSetting('field'),
      '#required' => TRUE,
      '#options' => $this->getAvailableFields(),
    );

    $enabled_plugins = array();
    $i = 0;
    foreach ($this->getSetting('provider_plugins') as $plugin_id => $plugin) {
      if ($plugin['checked']) {
        $plugin['weight'] = intval($i++);
        $enabled_plugins[$plugin_id] = $plugin;
      }
    }

    $elements['geocoder_plugins_title'] = array(
      '#type' => 'item',
      '#weight' => 15,
      '#title' => t('Geocoder plugin(s)'),
      '#description' => t('Select the Geocoder plugins to use, you can reorder them. The first one to return a valid value will be used.'),
    );

    $elements['provider_plugins'] = array(
      '#type' => 'table',
      '#weight' => 20,
      '#header' => array(
        array('data' => $this->t('Enabled')),
        array('data' => $this->t('Weight')),
        array('data' => $this->t('Name')),
      ),
      '#tabledrag' => array(
        array(
          'action' => 'order',
          'relationship' => 'sibling',
          'group' => 'provider_plugins-order-weight',
        ),
      ),
    );

    $rows = array();
    $count = count($enabled_plugins);
    foreach (Geocoder::getPlugins('Provider') as $plugin_id => $plugin_name) {
      if (isset($enabled_plugins[$plugin_id])) {
        $weight = $enabled_plugins[$plugin_id]['weight'];
      }
      else {
        $weight = $count++;
      }

      $rows[$plugin_id] = array(
        '#attributes' => array(
          'class' => array('draggable'),
        ),
        '#weight' => $weight,
        'checked' => array(
          '#type' => 'checkbox',
          '#default_value' => isset($enabled_plugins[$plugin_id]) ? 1 : 0,
        ),
        'weight' => array(
          '#type' => 'weight',
          '#title' => t('Weight for @title', array('@title' => $plugin_id)),
          '#title_display' => 'invisible',
          '#default_value' => $weight,
          '#attributes' => array('class' => array('provider_plugins-order-weight')),
        ),
        'name' => array(
          '#plain_text' => $plugin_name,
        ),
      );
    }

    uasort($rows, function($a, $b) {
      return strcmp($a['#weight'], $b['#weight']);
    });

    foreach ($rows as $plugin_id => $row) {
      $elements['provider_plugins'][$plugin_id] = $row;
    }

    $elements['dumper_plugin'] = array(
      '#type' => 'select',
      '#weight' => 25,
      '#title' => 'Output format',
      '#default_value' => $this->getSetting('dumper_plugin'),
      '#options' => Geocoder::getPlugins('dumper'),
      '#description' => t('Set the output format of the value. Ex, for a geofield, the format must be set to WKT.'),
    );

    $elements['delta_handling'] = array(
      '#type' => 'select',
      '#weight' => 30,
      '#title' => $this->t('Multi-value input handling'),
      '#description' => $this->t('Should geometries from multiple inputs be: <ul><li>Matched with each input (e.g. One POINT for each address field)</li><li>Aggregated into a single MULTIPOINT geofield (e.g. One MULTIPOINT polygon from multiple address fields)</li><li>Broken up into multiple geometries (e.g. One MULTIPOINT to multiple POINTs.)</li></ul>'),
      '#default_value' => $this->getSetting('delta_handling'),
      '#options' => $this->getDeltaHandling(),
      '#required' => TRUE,
    );

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = array();
    $available_fields = $this->getAvailableFields();
    $provider_plugin_ids = $this->getEnabledProviderPlugins();
    $delta_handling_options = $this->getDeltaHandling();
    $dumper_plugins = Geocoder::getPlugins('Dumper');
    $dumper_plugin = $this->getSetting('dumper_plugin');
    $field = $this->getSetting('field');
    $delta_handling = $this->getSetting('delta_handling');
    $mode = $this->getSetting('mode');

    if (!empty($available_fields[$field])) {
      $summary[] = $this->t('Field: @field', array('@field' => $available_fields[$field]));
    }
    if (!empty($provider_plugin_ids)) {
      $summary[] = t('Geocoder plugin(s): @plugin_ids', array('@plugin_ids' => implode(', ', $provider_plugin_ids)));
    }
    if (!empty($dumper_plugins[$dumper_plugin])) {
      $summary[] = t('Output format plugin: @format', array('@format' => $dumper_plugins[$dumper_plugin]));
    }
    if (!empty($delta_handling_options[$delta_handling])) {
      $summary[] = t('Delta handling: @delta', array('@delta' => $delta_handling_options[$delta_handling]));
    }

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    // See if we can use VALUE.
    $main_widget = $element + array(
      '#type' => 'hidden',
      '#default_value' => isset($items[$delta]->value) ? $items[$delta]->value : NULL,
    );

    $element['value'] = $main_widget;

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function errorElement(array $element, ConstraintViolationInterface $violation, array $form, FormStateInterface $form_state) {
    if ($violation->arrayPropertyPath == array('format') && isset($element['format']['#access']) && !$element['format']['#access']) {
      // Ignore validation errors for formats if formats may not be changed,
      // i.e. when existing formats become invalid. See filter_process_format().
      return FALSE;
    }
    return $element;
  }

}
