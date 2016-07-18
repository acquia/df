<?php

namespace Drupal\df_tools_map\Plugin\views\filter;

use Drupal\Core\Form\FormStateInterface;
use Drupal\views\Plugin\views\filter\FilterPluginBase;

/**
 * Simple sort handler for proximity.
 *
 * @ViewsFilter("df_tools_map_simple_proximity")
 */
class SimpleProximity extends FilterPluginBase {

  /**
   * {@inheritdoc}
   */
  var $no_operator = TRUE;

  /**
   * {@inheritdoc}
   */
  protected function defineOptions() {
    $options = parent::defineOptions();
    $options['exposed'] = ['default' => TRUE];
    $options['expose']['distance'] = ['default' => FALSE];
    $options['expose']['label'] = ['default' => t('Location')];
    return $options;
  }

  /**
   * {@inheritdoc}
   */
  public function defaultExposeOptions() {
    parent::defaultExposeOptions();
    $this->options['expose']['distance'] = FALSE;
  }

  /**
   * {@inheritdoc}
   */
  protected function valueForm(&$form, FormStateInterface $form_state) {
    $form['value'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Location'),
      '#attributes' => [
        'class' => ['simple-proximity-location'],
        'placeholder' => t('Enter your address or zip code.')
      ],
      '#size' => 30,
      '#default_value' => $this->value,
      '#prefix' => '<div class="simple-proximity-wrapper">',
      '#suffix' => '</div>',
    ];

    // Don't show this element in the context of an admin menu.
    if (!isset($form['admin_label'])) {
      $form['value']['#suffix'] =
        '<a class="simple-proximity-location-button button" title="' . $this->t('Use your current location') . '">' .
        '  <i class="fa fa-map-marker" aria-hidden="true"></i>' .
        '</a>' . $form['value']['#suffix'];
      $form['value']['#attached'] = ['library' => ['dfs_fin_location/main']];
    }
  }

  /**
   * {@inheritdoc}
   */
  public function buildExposeForm(&$form, FormStateInterface $form_state) {
    $form['expose']['label'] = [
      '#type' => 'textfield',
      '#default_value' => $this->options['expose']['label'],
      '#title' => $this->t('Label'),
      '#size' => 30,
    ];

    $form['expose']['distance'] = [
      '#type' => 'textfield',
      '#default_value' => $this->options['expose']['distance'],
      '#title' => $this->t('Minimum distance (in kilometers)'),
      '#description' => $this->t('If you leave this value empty results will be sorted by distance.'),
      '#size' => 30,
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function validateExposeForm($form, FormStateInterface $form_state) {}

  /**
   * {@inheritdoc}
   */
  public function submitExposeForm($form, FormStateInterface $form_state) {
    $this->options['expose']['distance'] = $form_state->getValue('options')['expose']['distance'];
  }

  /**
   * Called to add the sort to a query.
   */
  public function query() {
    $this->ensureMyTable();
    $location = $this->value[0];

    // Geocode the location if it's present.
    if (!empty($location)) {
      if ($collection = \Drupal::service('geocoder')->geocode($location,['googlemaps'])) {
        $coordinates = $collection->first()->getCoordinates();

        // Generate a distance formula programatically.
        $haversine_options = [
          'origin_latitude' => $coordinates->getLatitude(),
          'origin_longitude' => $coordinates->getLongitude(),
          'destination_latitude' => $this->table . '.' . $this->realField . '_lat',
          'destination_longitude' => $this->table . '.' . $this->realField . '_lon',
          'earth_radius' => GEOFIELD_KILOMETERS,
        ];

        $formula = $this->haversine($haversine_options);

        /** @var \Drupal\views\Plugin\views\query\Sql $query */
        $query = $this->query;
        $query->addOrderBy(NULL, $formula, 'ASC', $this->table . '_simple_proximity');

        // Add a where expression if a distance is used.
        if ($this->options['expose']['distance']) {
          $distance = $this->options['expose']['distance'];
          $placeholder = $query->placeholder($this->table . '_simple_proximity');
          $query->addWhereExpression(0, '(' . $formula . ' <= ' . $placeholder . ')', [$placeholder => $distance]);
        }
      }
    }
  }

  /**
   * Port of geofield_haversine() to Drupal 8.
   *
   * @param $options
   *
   * @return mixed|string
   */
  protected function haversine($options) {
    $formula = '( :earth_radius * ACOS( COS( RADIANS(:origin_latitude) ) * COS( RADIANS(:destination_latitude) ) * COS( RADIANS(:destination_longitude) - RADIANS(:origin_longitude) ) + SIN( RADIANS(:origin_latitude) ) * SIN( RADIANS(:destination_latitude) ) ) )';

    foreach ($options as $key => $option) {
      if (is_numeric($option)) {
        $formula = str_replace(':' . $key, $option, $formula);
      }
      else {
        $formula = str_replace(':' . $key, db_escape_field($option), $formula);
      }
    }

    return $formula;
  }

}
