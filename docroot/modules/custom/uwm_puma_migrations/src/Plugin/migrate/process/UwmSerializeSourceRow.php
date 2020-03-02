<?php

namespace Drupal\uwm_puma_migrations\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Perform custom value transformations.
 *
 * @MigrateProcessPlugin(
 *   id = "uwm_serialize_source_row"
 * )
 *
 * To do custom value transformations use the following:
 *
 * @code
 * field_res_text:
 *   plugin: uwm_serialize_source_row
 *   source: text
 * @endcode
 */
class UwmSerializeSourceRow extends ProcessPluginBase {

  /**
   * {@inheritdoc}
   *
   * Returns json encoded string of source row values.
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {

    $return_value = [];

    /***
     * Get the source values for the row.
     * Since rows include other metadata, we create an
     * array only the columns named in the migration yml.
     */
    $row_source = $row->getSource();
    $row_values = [];
    foreach ($row_source['column_names'] as $csv_column) {
      $key = key($csv_column);
      $row_values[$key] = $row_source[$key];
    }

    if ($row_values) {
      /***
       * Ensure we return object in an array, ie [{...}], so a collection can
       * be made from multiple rows later and result in [{...}, {...}, {...}]
       * for multiple CSV rows matching the same destination node.
       ***/
      $return_value = \GuzzleHttp\json_encode([$row_values]);
    }

    return $return_value;

  }

}
