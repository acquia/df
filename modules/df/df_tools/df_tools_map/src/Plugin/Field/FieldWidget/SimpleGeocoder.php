<?php

namespace Drupal\df_tools_map\Plugin\Field\FieldWidget;

use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Field\WidgetBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Plugin implementation of the 'df_tools_map_simple_geocoder' widget.
 *
 * @FieldWidget(
 *   id = "df_tools_map_simple_geocoder",
 *   label = @Translation("Simple Geocoder"),
 *   field_types = {
 *     "geofield"
 *   },
 * )
 */
class SimpleGeocoder extends WidgetBase {

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'source_field' => '',
      'show_coordinates' => TRUE,
    ] + parent::defaultSettings();
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements = parent::settingsForm($form, $form_state);

    /** @var \Drupal\Core\Entity\EntityFieldManagerInterface $entity_field_manager */
    $entity_field_manager = \Drupal::service('entity_field.manager');
    $entity_field_definitions = $entity_field_manager->getFieldDefinitions($this->fieldDefinition->getTargetEntityTypeId(), $this->fieldDefinition->getTargetBundle());

    $options = [];
    foreach ($entity_field_definitions as $id => $definition) {
      if ($definition->getType() == 'string' || $definition->getType() == 'address') {
        $options[$id] = $definition->getLabel();
      }
    }

    $elements['source_field'] = [
      '#type' => 'select',
      '#title' => $this->t('Source Field'),
      '#default_value' => $this->getSetting('source_field'),
      '#required' => TRUE,
      '#options' => $options,
      '#description' => t('The source Text or Address field to Geocode from.')
    ];

    $elements['show_coordinates'] = [
      '#type' => 'checkbox',
      '#title' => t('Show Coordinates'),
      '#default_value' => $this->getSetting('show_coordinates'),
      '#description' => t('Whether or not the current coordinates should be shown in the form.')
    ];

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];

    $summary[] = $this->t('Source Field: @source', ['@source' => $this->getSetting('source_field')]);
    $coordinates = $this->getSetting('show_coordinates') ? 'shown' : 'hidden';
    $summary[] = $this->t('Coordinates are ' . $coordinates);

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function formElement(FieldItemListInterface $items, $delta, array $element, array &$form, FormStateInterface $form_state) {
    // The user has the option of hiding the current coordinates from view.
    if ($this->getSetting('show_coordinates')) {
      $element += [
        '#type' => 'textfield',
        '#disabled' => TRUE,
        '#placeholder' => t('Latitude: @lat, Longitude: @lon', ['@lat' => $items[$delta]->lat, '@lon' => $items[$delta]->lon]),
        '#suffix' => t('These values are set dynamically on submit from the @field field.', ['@field' => $this->getSetting('source_field')]),
      ];
    }
    else {
      // We set this field dynamically, no need to have more than the minimum.
      $element += [
        '#type' => 'hidden',
        '#value' => ''
      ];
    }

    return $element;
  }

  /**
   * {@inheritdoc}
   */
  public function massageFormValues(array $values, array $form, FormStateInterface $form_state) {
    $source_field = $this->getSetting('source_field');

    // Check that the source field has been set.
    if (!empty($source_field)) {
      // Get the value of our source field.
      $field_value = $form_state->getValue($source_field, []);

      // For each value, geocode the address and set our coordinates.
      foreach ($field_value as $delta => $value) {
        // Check if this field is an Address field.
        if (isset($value['address_line1'])) {
          $address_line2 = !empty($value['address_line2']) ? $value['address_line2'] . "\n" : '';
          $value['value'] = $value['address_line1'] . "\n" .
            $address_line2 .
            $value['locality'] . ', ' .
            str_replace('US-', '', $value['administrative_area']) . ' ' .
            $value['postal_code'] . "\n" .
            $value['country_code'];
        }

        // Geocode the source field's value.
        if ($collection = \Drupal::service('geocoder')->geocode($value['value'], ['googlemaps'])) {
          // Set our value in a similar way to Geofield's LatLon Widget.
          // @see \Drupal\geofield\Plugin\Field\FieldWidget\GeofieldLatLonWidget::massageFormValues()
          $coordinates = $collection->first()->getCoordinates();
          $point = [$coordinates->getLongitude(),  $coordinates->getLatitude()];
          $values[$delta]['value'] = \Drupal::service('geofield.wkt_generator')->WktBuildPoint($point);
        }
      }
    }

    return $values;
  }

}
