<?php

namespace Drupal\uwm_res_import\Plugin\migrate\process;

use Drupal\migrate\MigrateException;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;


/**
 * Perform custom value transformations.
 *
 * @MigrateProcessPlugin(
 *   id = "uwm_term_value1"
 * )
 *
 * To do custom value transformations use the following:
 *
 * @code
 * field_text:
 *   plugin: transform_value
 *   source: text
 * @endcode
 *
 */
class UwmTermValue1 extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {


    //    if (!empty($value[0]['type']) && stripos($value[0]['type'], 'taxonomy_term') !== FALSE) {


    $term = \Drupal::service('entity.repository')
      ->loadEntityByUuid('taxonomy_term', $value['id']);

    if ($term && $term->id()) {
      return $term->name->value;
    }

    return NULL;

    // }


  }


}
