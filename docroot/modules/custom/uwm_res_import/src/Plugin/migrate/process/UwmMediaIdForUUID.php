<?php

namespace Drupal\uwm_res_import\Plugin\migrate\process;

use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;

/**
 * Perform custom value transformations. Fetches a local id using a remote uuid.
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

      /***
       * If given the UUID for a video on the remote system, check our
       * migration table and provide the local media id.
       */
      $query = \Drupal::database()
        ->select('migrate_map_uwm_res_media_remote_videos', 'migrate_map');
      $query->addField('migrate_map', 'destid1', 'migrate_map_destination_id');
      $query->condition('sourceid1', $value);

      $results = $query->execute()->fetchAll();

      if (!empty($results[0])) {
        return $results[0]->migrate_map_destination_id ?? NULL;
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
