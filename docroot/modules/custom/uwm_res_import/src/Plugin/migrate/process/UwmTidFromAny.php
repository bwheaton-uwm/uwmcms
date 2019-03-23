<?php

namespace Drupal\uwm_res_import\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Perform custom value transformations.
 *
 * @MigrateProcessPlugin(
 *   id = "uwm_tid_from_any"
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
class UwmTidFromAny extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {

    if ($value) {

      $value = !empty($value['id']) ? $value['id'] : $value;

      $term = \Drupal::service('entity.repository')
        ->loadEntityByUuid('taxonomy_term', $value);

      if (!$term) {

        $term = \Drupal::entityTypeManager()
          ->getStorage('taxonomy_term')
          ->loadByProperties(['uwm_unique_id' => $value]);
      }

      if ($term && $term->id()) {
        return $term->id();
      }

    }
    return NULL;
  }

  /**
   * {@inheritdoc}
   */
  public function prepareRow(Row $row) {
    // $tid = $row->getSourceProperty('tid');.
  }

}
