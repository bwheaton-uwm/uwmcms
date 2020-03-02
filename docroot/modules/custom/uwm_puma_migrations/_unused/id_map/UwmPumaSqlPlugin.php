<?php

namespace Drupal\uwm_puma_migrations\Plugin\migrate\id_map;

use Drupal\migrate\Plugin\migrate\id_map\Sql;

/**
 * Defines the sql based ID map implementation.
 *
 * It creates one map and one message table per migration entity to store the
 * relevant information.
 *
 * @PluginID("uwm_puma_sql_plugin")
 */
class UwmPumaSqlPlugin extends Sql {

  /**
   * {@inheritdoc}
   */
  protected function getFieldSchema(array $id_definition) {

    $schema = parent::getFieldSchema($id_definition);
    if ($schema['type'] == 'varchar') {
      $schema['length'] = 256;
    }

    return $schema;

  }

  /**
   * {@inheritdoc}
   */
  public function lookupDestinationId(array $source_id_values) {
    // Return [];.
  }

  /**
   * {@inheritdoc}
   */
  public function lookupDestinationIds(array $source_id_values) {
    // Return [];.
  }

  /**
   * {@inheritdoc}
   */
  public function lookupSourceId(array $destination_id_values) {
    // Return [];.
  }

}
