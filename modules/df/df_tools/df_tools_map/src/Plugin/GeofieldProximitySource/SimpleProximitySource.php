<?php

namespace Drupal\df_tools_map\Plugin\GeofieldProximitySource;

use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\ReplaceCommand;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Logger\LoggerChannelTrait;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\df_tools_map\Plugin\views\filter\SimpleProximity;
use Drupal\geofield\Plugin\GeofieldProximitySourceBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines 'Simple Proximity Source' plugin.
 *
 * @package Drupal\geofield\Plugin
 *
 * @GeofieldProximitySource(
 *   id = "simple_proximity_source",
 *   label = @Translation("Simple Proximity Source"),
 *   description = @Translation("A source plugin that calculates proximity based on a simple proximity filter."),
 *   exposedDescription = @Translation("The origin is calculated from a simple proximity filter."),
 *   context = {
 *     "sort",
 *     "field",
 *   }
 * )
 */
class SimpleProximitySource extends GeofieldProximitySourceBase implements ContainerFactoryPluginInterface {

  use LoggerChannelTrait;

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition
    );
  }

  /**
   * Returns the list of available proximity filters.
   *
   * @return array
   *   The list of available proximity filters.
   */
  protected function getAvailableProximityFilters() {
    $proximity_filters = [];

    /** @var \Drupal\views\Plugin\views\filter\FilterPluginBase $filter */
    foreach ($this->viewHandler->displayHandler->getHandlers('filter') as $delta => $filter) {
      if ($filter instanceof SimpleProximity) {
        $proximity_filters[$delta] = $filter->adminLabel();
      }
    }

    return $proximity_filters;
  }

  /**
   * {@inheritdoc}
   */
  public function buildOptionsForm(array &$form, FormStateInterface $form_state, array $options_parents, $is_exposed = FALSE) {
    $user_input = $form_state->getUserInput();
    $proximity_filters_sources = $this->getAvailableProximityFilters();
    $user_input_proximity_filter = $user_input['options']['source_configuration']['source_proximity_filter'] ?? current(array_keys($proximity_filters_sources));
    $source_proximity_filter = $this->configuration['source_proximity_filter'] ?? $user_input_proximity_filter;

    if (!empty($proximity_filters_sources)) {
      $form['source_proximity_filter'] = [
        '#type' => 'select',
        '#title' => $this->t('Source Proximity Filter'),
        '#description' => $this->t('Select the proximity filter to use as the starting point for calculating proximity.'),
        '#options' => $this->getAvailableProximityFilters(),
        '#default_value' => $source_proximity_filter,
        '#ajax' => [
          'callback' => [static::class, 'sourceProximityFilterUpdate'],
          'effect' => 'fade',
        ],
      ];
    }
    else {
      $form['source_proximity_filter_warning'] = [
        '#type' => 'html_tag',
        '#tag' => 'div',
        '#value' => $this->t('No Simple Proximity Filter found. At least one should be set for this Proximity Field to work.'),
        "#attributes" => [
          'class' => ['geofield-warning', 'red'],
        ],
      ];
      $form_state->setError($form['source_proximity_filter_warning'], $this->t('This Proximity Field cannot work. Dismiss this and add & setup a Simple Proximity Filter before.'));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function validateOptionsForm(array &$form, FormStateInterface $form_state, array $options_parents) {
    $values = $form_state->getValues();
    if (!isset($values['options']['source_configuration']['source_proximity_filter'])) {
      $form_state->setError($form['source_proximity_filter_warning'], $this->t('This Proximity Field cannot work. Dismiss this and add and setup a Proximity Filter before.'));
    }
  }

  /**
   * Ajax callback triggered on Proximity Filter Selection.
   *
   * @param array $form
   *   The build form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   *
   * @return \Drupal\Core\Ajax\AjaxResponse
   *   Ajax response with updated form element.
   */
  public static function sourceProximityFilterUpdate(array $form, FormStateInterface $form_state) {
    $response = new AjaxResponse();
    $response->addCommand(new ReplaceCommand(
      '#proximity-source-configuration',
      $form['options']['source_configuration']
    ));
    return $response;
  }

  /**
   * {@inheritdoc}
   */
  public function getOrigin() {
    $origin = [];

    if (
      isset($this->viewHandler)
      && isset($this->viewHandler->view->filter[$this->viewHandler->options['source_configuration']['source_proximity_filter']])
      && $source_proximity_filter = $this->viewHandler->options['source_configuration']['source_proximity_filter']
    ) {
      /** @var \Drupal\df_tools_map\Plugin\views\filter\SimpleProximity $simple_proximity_filter */
      $simple_proximity_filter = $this->viewHandler->view->filter[$source_proximity_filter];
      $provider = \Drupal::entityTypeManager()->getStorage('geocoder_provider')->load('googlemaps');
      try {
        $location = $simple_proximity_filter->value;
        if ($collection = \Drupal::service('geocoder')->geocode($location[0], [$provider])) {
          $coordinates = $collection->first()->getCoordinates();
          $origin = [
            "lat" => $coordinates->getLatitude(),
            "lon" => $coordinates->getLongitude(),
          ];
        }
      }
      catch (\Exception $e) {
        $this->getLogger('df_tools_map')->error($e->getMessage());
      }
    }
    return $origin;
  }

}
