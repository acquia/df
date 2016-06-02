<?php
/**
 * @file
 * The Data Prepare plugin.
 */

namespace Drupal\geocoder\Plugin\Geocoder;

use Drupal\Core\Entity\EntityInterface;
use Drupal\geocoder\Plugin\Geocoder\DataPrepareInterface;
use Drupal\geocoder\Plugin\GeocoderPluginBase;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class DataPrepare.
 */
abstract class DataPrepareBase extends GeocoderPluginBase implements DataPrepareInterface {
  /**
   * @var EntityInterface
   */
  private $entity;

  /**
   * @var string
   */
  private $field_id;

  /**
   * @var array
   */
  private $values;

  /**
   * @var string[]
   */
  private $widget_ids;

  /**
   * @var array
   */
  private $widget_configuration;

  /**
   * @inheritDoc
   */
  public function setEntity(EntityInterface $entity) {
    $this->entity = $entity;

    return $this;
  }

  /**
   * @inheritDoc
   */
  public function getEntity() {
    return $this->entity;
  }

  /**
   * @inheritDoc
   */
  public function setWidgetIds(array $widgets = array()) {
    $this->widget_ids = $widgets;

    return $this;
  }

  /**
   * @inheritDoc
   */
  public function getWidgetIds() {
    return $this->widget_ids;
  }

  /**
   * @inheritDoc
   */
  public function setValues(array $values = array()) {
    $this->values = $values;

    return $this;
  }

  /**
   * @inheritDoc
   */
  public function getValues() {
    return $this->values;
  }

  /**
   * @inheritDoc
   */
  public function setWidgetConfiguration(array $settings = array()) {
    $this->widget_configuration = $settings;

    return $this;
  }

  /**
   * @inheritDoc
   */
  public function getWidgetConfiguration() {
    return $this->widget_configuration;
  }

  /**
   * @inheritDoc
   */
  public function setCurrentField($field_id) {
    $this->field_id = $field_id;

    return $this;
  }

  /**
   * @inheritDoc
   */
  public function getCurrentField() {
    return $this->field_id;
  }

  /**
   * @inheritDoc
   */
  public function getPreparedGeocodeValues(array $values = array()) {
    return $this->setValues($values)->getValues();
  }

  /**
   * @inheritDoc
   */
  public function getPreparedReverseGeocodeValues(array $values = array()) {
    return $this->setValues($values)->getValues();
  }

  /**
   * @inheritdoc
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition
    );
  }

}
