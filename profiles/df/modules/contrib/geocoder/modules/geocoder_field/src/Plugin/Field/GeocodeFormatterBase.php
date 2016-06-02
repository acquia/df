<?php

/**
 * @file
 * Contains \Drupal\geocoder_field\Plugin\Field\GeocoderFormatterBase.
 */

namespace Drupal\geocoder_field\Plugin\Field;

use Drupal\Core\Field\FormatterBase;
use Drupal\Core\Field\FieldItemListInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\geocoder\Geocoder;

/**
 * Base Plugin implementation of the Geocode formatter.
 */
abstract class GeocodeFormatterBase extends FormatterBase {
  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return parent::defaultSettings() + array(
      'dumper_plugin' => 'wkt',
      'provider_plugins' => array(),
    );
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm(array $form, FormStateInterface $form_state) {
    $elements = parent::settingsForm($form, $form_state);

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

    return $elements;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = array();
    $provider_plugin_ids = $this->getEnabledProviderPlugins();
    $dumper_plugins = Geocoder::getPlugins('Dumper');
    $dumper_plugin = $this->getSetting('dumper_plugin');

    if (!empty($provider_plugin_ids)) {
      $summary[] = t('Geocoder plugin(s): @plugin_ids', array('@plugin_ids' => implode(', ', $provider_plugin_ids)));
    }
    if (!empty($dumper_plugin)) {
      $summary[] = t('Output format plugin: @format', array('@format' => $dumper_plugins[$dumper_plugin]));
    }

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function viewElements(FieldItemListInterface $items, $langcode) {
    $elements = array();
    $dumper = \Drupal::service('geocoder.dumper.' . $this->getSetting('dumper_plugin'));
    $provider_plugins = $this->getEnabledProviderPlugins();

    foreach ($items as $delta => $item) {
      if ($addressCollection = Geocoder::geocode($provider_plugins, $item->value)) {
        $elements[$delta] = array(
          '#plain_text' => $dumper->dump($addressCollection->first()),
        );
      }
    }

    return $elements;
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

}
