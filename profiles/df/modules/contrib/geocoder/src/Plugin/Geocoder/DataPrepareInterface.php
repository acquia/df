<?php
/**
 * @file
 * The Data Prepare Interface.
 */

namespace Drupal\geocoder\Plugin\Geocoder;
use Drupal\Component\Plugin\PluginInspectionInterface;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;

/**
 * Interface DataPrepareInterface.
 */
interface DataPrepareInterface extends PluginInspectionInterface, ContainerFactoryPluginInterface {
  /**
   * Set the input values.
   *
   * @param array $values
   *
   * @return $this
   */
  public function setValues(array $values = array());

  /**
   * Get the input values.
   *
   * @return array
   */
  public function getValues();

  /**
   * Set the entity to work on.
   *
   * @return $this
   */
  public function setEntity(EntityInterface $entity);

  /**
   * Get the entity.
   *
   * @return EntityInterface
   */
  public function getEntity();

  /**
   * @param array $widgets
   *
   * @return $this
   */
  public function setWidgetIds(array $widgets = array());

  /**
   * @return array
   */
  public function getWidgetIds();

  /**
   * @param array $settings
   *
   * @return $this
   */
  public function setWidgetConfiguration(array $settings = array());

  /**
   * @return mixed
   */
  public function getWidgetConfiguration();

  /**
   * @param $field_id
   *
   * @return $this
   */
  public function setCurrentField($field_id);

  /**
   * @return string
   */
  public function getCurrentField();

  /**
   * @return mixed
   */
  public function getPreparedGeocodeValues(array $values = array());

  /**
   * @return mixed
   */
  public function getPreparedReverseGeocodeValues(array $values = array());

}
