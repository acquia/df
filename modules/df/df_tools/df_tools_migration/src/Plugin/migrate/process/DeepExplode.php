<?php

namespace Drupal\df_tools_migration\Plugin\migrate\process;

use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\MigrateException;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\Row;

/**
 * This plugin explodes a delimited string into an array of arrays.
 *
 * @MigrateProcessPlugin(
 *   id = "deep_explode"
 * )
 */
class DeepExplode extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {
    if (is_string($value)) {
      if (!empty($this->configuration['delimiter']) && !empty($this->configuration['nested_delimiter'])) {
        $items = explode($this->configuration['delimiter'], $value);
        $new_value = [];
        foreach ($items as $item) {
          if (is_string($item)) {
            $deep_item = explode($this->configuration['nested_delimiter'], $item);
            if (isset($this->configuration['nested_keys'])) {
              $deep_item = array_combine($this->configuration['nested_keys'], $deep_item);
            }
            $new_value[] = $deep_item;
          }
        }
        return $new_value;
      }
      else {
        throw new MigrateException('delimiter is empty');
      }
    }
    else {
      throw new MigrateException(sprintf('%s is not a string', var_export($value, TRUE)));
    }
  }

  /**
   * {@inheritdoc}
   */
  public function multiple() {
    return TRUE;
  }

}
