<?php

namespace Drupal\uwm_res_import\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Perform custom value transformations.
 *
 * @MigrateProcessPlugin(
 *   id = "uwm_media_id_for_uuid"
 * )
 *
 * To do custom value transformations use the following:
 *
 * @code
 * field_text:
 *   plugin: transform_value
 *   source: text
 * @endcode
 */
class UwmMediaIdForUUID extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {

    if ($value) {

      $value = !empty($value['id']) ? $value['id'] : $value;
      $entities = \Drupal::entityTypeManager()->getStorage('media')
        ->loadByProperties(['uuid' => $value]);

      if (!empty($entities) && $entity = reset($entities)) {
        return $entity->id();
      }

      return NULL;
    }

  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {

  }

}
