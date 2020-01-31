<?php

namespace Drupal\uwm_import_epic\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\migrate\Plugin\MigrationInterface;
use Drupal\migrate\Plugin\MigrateIdMapInterface;
use Drupal\migrate\Event\MigrateEvents;
use Drupal\migrate\Event\MigrateImportEvent;
use Drupal\migrate\Event\MigratePreRowSaveEvent;
use Drupal\migrate\MigrateException;

/**
 * Class ProviderSchedulingMigrationSubscriber.
 *
 * While importing the the provider scheduling data from Epic, validate the
 * data, automatically update some values, and generate a log file.
 *
 * @see ProviderSchedulingDataValidator
 *
 * @TODO inject the ProviderSchedulingDataValidator service.
 */
class ProviderSchedulingMigrationSubscriber implements EventSubscriberInterface {

  /**
   * ID of the migration we want to interact with.
   */
  const MIGRATION_ID = 'uwm_epic_provider_scheduling';

  /**
   * ID of the batch that's currently running this migration via the UI, if any.
   *
   * @var int
   */
  protected $batchId = NULL;

  /**
   * Helper to get the current batch if it is running this migration import.
   *
   * @return array|null
   *   The current batch array, if it's running this migration, or NULL if
   *   either no batch is running or the batch is doing something else.
   */
  protected function getMigrationBatch() {

    // Check if there's an active batch and it's running this migration import.
    $batch = batch_get();

    // The 'results' use the migration ID as the key.
    if (!empty($batch) && !empty($batch['sets'][$batch['current_set']]['results']) && isset($batch['sets'][$batch['current_set']]['results'][self::MIGRATION_ID])) {

      $this->batchId = $batch['id'];
      return $batch;

    }

    return NULL;

  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {

    $events = [];

    // Event when beginning a migration import.
    $events[MigrateEvents::PRE_IMPORT][] = ['onMigratePreImport'];

    // Event when about to import a single item.
    $events[MigrateEvents::PRE_ROW_SAVE][] = ['onMigratePreRowSave'];

    // Event when finishing migration import.
    $events[MigrateEvents::POST_IMPORT][] = ['onMigratePostImport'];

    return $events;

  }

  /**
   * Run handling on migrate pre-import event.
   *
   * At the beginning of the entire import, initialize the provider scheduling
   * validation log.
   *
   * NOTE: When import is run via Drush, this event occurs once at the beginning
   * of the entire import (before any rows are processed). When run via the
   * admin UI, it occurs at the beginning of each batch.
   *
   * @param \Drupal\migrate\Event\MigrateImportEvent $event
   *   The import event object.
   */
  public function onMigratePreImport(MigrateImportEvent $event) {

    /** @var \Drupal\migrate\Plugin\MigrationInterface $migration */
    $migration = $event->getMigration();

    if ($migration->getPluginId() !== self::MIGRATION_ID) {
      return;
    }

    // Determine if this is really the beginning of the entire import, and not
    // the beginning of a batch (other than the first).
    $import_beginning = NULL;

    // Check for a current batch running this migration import.
    if ($batch = $this->getMigrationBatch()) {

      // Check the counter which tracks total number of rows processed across
      // all batches.
      if ($batch['sets'][$batch['current_set']]['sandbox']['counter'] === 0) {

        $import_beginning = 'batch';

      }

    }
    else {

      // If this migration batch does not exist currently, assume running via
      // Drush.
      $import_beginning = 'drush';

    }

    if (!empty($import_beginning)) {

      // Start a new log for this import run.
      // Tell the service to use temp store if this is a batch run.
      \Drupal::service('uwm_import_epic.provider_scheduling_data_validator')->initLog(($import_beginning === 'batch'));

    }

  }

  /**
   * Run handling on migrate pre-row-save event.
   *
   * Validate each source row of a provider's scheduling data from Epic, in
   * combination with accepting-patients field values on node.
   *
   * @param \Drupal\migrate\Event\MigratePreRowSaveEvent $event
   *   The import event object.
   *
   * @throws \Drupal\migrate\MigrateException
   *   If the provider NPI does not match to an existing Drupal node, throw
   *   exception to skip importing this row.
   */
  public function onMigratePreRowSave(MigratePreRowSaveEvent $event) {

    /** @var \Drupal\migrate\Plugin\MigrationInterface $migration */
    $migration = $event->getMigration();

    if ($migration->getPluginId() !== self::MIGRATION_ID) {
      return;
    }

    // Map convenient names to (Drupal node) field names.
    $f = [
      'accepting_returning' => 'field_res_isacceptingreturnpts',
      'accepting_new' => 'field_res_isacceptingnewpts',
      'epic_id' => 'field_res_ser_id',
      'direct_enabled' => 'field_res_is_direct_scheduling',
      'open_enabled' => 'field_res_is_open_scheduling',
      'visit_type_ids' => 'field_res_visit_type_id',
    ];

    /** @var \Drupal\migrate\Row $row */
    $row = $event->getRow();

    // Retrieve values incoming from csv row.
    // Example:
    // [
    // "epic_id" => "12345"
    // "npi" => "9876543"
    // "name" => "DOE, JANE"
    // "booking_ids" => "1234|5432"
    // "enabled_open_sched" => "Yes"
    // "enabled_direct_sched" => "Yes"
    // "fields" => []
    // "keys" => array:1
    // "column_names" => array:6
    // "header_row_count" => 1
    // "file_flags" => 15
    // "delimiter" => ","
    // "enclosure" => """
    // "escape" => "\"
    // "path" => "/var/www/biosres/docs/epic-providers.csv"
    // "file_class" => "Drupal\migrate_source_csv\CSVFileObject"
    // "plugin" => "csv"
    // ].
    $source_values = $row->getSource();

    // Retrieve values to be saved to node.
    // Example:
    // [
    // "nid" => "321"
    // "field_res_ser_id" => "12345"
    // "field_res_visit_type_id" => [
    // 0 => "1234"
    // 1 => "5432"
    // ]
    // "field_res_is_open_scheduling" => 1
    // "field_res_is_direct_scheduling" => 1
    // ].
    $dest_values = $row->getDestination();

    // Get current field values from the node.
    // Boolean field values are converted from 1 and 0 to TRUE and FALSE.
    $node_values = [
      'accepting_returning' => NULL,
      'accepting_new' => NULL,
      'epic_id' => NULL,
      'direct_enabled' => NULL,
      'open_enabled' => NULL,
      'visit_type_ids' => NULL,
    ];

    if (!empty($dest_values['nid'])) {

      /** @var \Drupal\node\NodeInterface $node */
      $node = \Drupal::entityTypeManager()->getStorage('node')->load($dest_values['nid']);
      if (!empty($node)) {
        foreach (array_keys($node_values) as $key) {
          $field_name = $f[$key];

          if ($node->hasField($field_name) && !$node->get($field_name)->isEmpty()) {

            $field_value = $node->get($field_name)->first()->getValue()['value'];

            if (in_array($field_name, [
              'field_res_isacceptingreturnpts',
              'field_res_isacceptingnewpts',
              'field_res_is_direct_scheduling',
              'field_res_is_open_scheduling',
            ])) {
              $field_value = (boolean) $field_value;
            }

            $node_values[$key] = $field_value;

          }
        }
      }

    }

    // Pass 'is accepting returning patients' and 'is accepting new patients'
    // current node field values into the destination values.
    // These are NOT imported - but may need to be automatically changed
    // according to logic based on imported values. To allow that, they are
    // included in the migration's `overwrite_properties`.
    // Initialize them to their current values from the node - this is critical
    // to preserve current data!
    // @see code below, after validation.
    if (!empty($node)) {

      $row->setDestinationProperty($f['accepting_returning'], (int) $node_values['accepting_returning']);
      $row->setDestinationProperty($f['accepting_new'], (int) $node_values['accepting_new']);

    }

    // Collect arguments to pass to validator.
    $identifiers = [
      'nid' => isset($dest_values['nid']) ? $dest_values['nid'] : NULL,
      'npi' => !empty($source_values['npi']) ? $source_values['npi'] : NULL,
      'name' => !empty($source_values['name']) ? $source_values['name'] : "",
    ];

    // Note: for direct and open scheduling boolean values - if the source value
    // did not map to `1` or `0`, the processor defaults it to an empty string,
    // so compare specifically to that to detect that the source value is
    // missing or invalid.
    $values = [
      'accepting_returning' => $node_values['accepting_returning'],

      'accepting_new' => $node_values['accepting_new'],

      'epic_id' => (isset($dest_values[$f['epic_id']]) && !empty($dest_values[$f['epic_id']])) ? $dest_values[$f['epic_id']] : NULL,

      'direct_enabled' => (isset($dest_values[$f['direct_enabled']]) && $dest_values[$f['direct_enabled']] !== "") ? (boolean) $dest_values[$f['direct_enabled']] : NULL,

      'open_enabled' => (isset($dest_values[$f['open_enabled']]) && $dest_values[$f['open_enabled']] !== "") ? (boolean) $dest_values[$f['open_enabled']] : NULL,

      'visit_type_ids' => isset($dest_values[$f['visit_type_ids']]) ? $dest_values[$f['visit_type_ids']] : NULL,
    ];

    // Validate this provider's data.
    // If the return value is not TRUE, it is the log (array) of issues found.
    // Depending on the issues, potentially skip importing this row, or adjust
    // the values to be imported.
    // Note: this replaces potential use of the `skip_on_empty` plugin for
    // most of the source values. Why implement our own version?
    // - The `skip_on_empty` plugin may have a message configured to be recorded
    //   and viewed in the 'Messages' tab in UI, but those are not successfully
    //   saved when the import is run in batches via UI.
    // - By including these issues into our custom log, all data issues are
    //   logged in one place.
    // TODO: add context arg.
    $validation_result = \Drupal::service('uwm_import_epic.provider_scheduling_data_validator')->validateOne($identifiers, $values, $node_values);

    if ($validation_result !== TRUE) {

      // Handle specific validation issues.
      if (isset($validation_result['incomplete_no']['npi']) || isset($validation_result['incomplete_no']['nid'])) {

        // Row cannot be imported because the NPI did not map to a node.
        // The pre-row-save event is dispatched within a `try` block, with a
        // `catch` for `MigrateException`. Throwing this exception bypasses
        // the next piece of code that would save to the destination node.
        // @see \Drupal\migrate\MigrateExecutable::import()
        throw new MigrateException(
          // $message = NULL.
          "Not imported - see Provider Scheduling Data Validation log",
          // $code - appears unused, so pass default value.
          0,
          // \Exception $previous - appears unused, so pass default value.
          NULL,
          // Mark as 'warning' as something is likely wrong in source data.
          MigrationInterface::MESSAGE_WARNING,
          // Mark row as 'ignored', as the `skip_on_empty` process plugin does.
          MigrateIdMapInterface::STATUS_IGNORED
        );

      }
      else {

        // Automatic logic:
        // If validation indicated to automatically update field values based
        // on others in the import, set them on the row here.
        if (isset($validation_result['auto_change']['accepting_returning'])) {

          $row->setDestinationProperty($f['accepting_returning'], (int) $validation_result['auto_change']['accepting_returning']);

        }

        if (isset($validation_result['auto_change']['accepting_new'])) {

          $row->setDestinationProperty($f['accepting_new'], (int) $validation_result['auto_change']['accepting_new']);

        }

        // Partial import:
        // Some (required) values were missing in source row. Import the values
        // that were present and fill in missing values with the existing field
        // value from the node.
        // This is critical - avoids overwriting these values to empty!
        if (isset($validation_result['incomplete_partial']['direct_enabled'])) {

          // Note: we cannot use $row->removeDestinationProperty() - the
          // removed property is re-added (because it's in the migration's
          // `overwrite_properties`) with empty value.
          $row->setDestinationProperty($f['direct_enabled'], (int) $node_values['direct_enabled']);

        }

        if (isset($validation_result['incomplete_partial']['open_enabled'])) {

          $row->setDestinationProperty($f['open_enabled'], (int) $node_values['open_enabled']);

        }

      }

    }

  }

  /**
   * Run handling on migrate post-import event.
   *
   * At the end of the entire import, write out the provider scheduling
   * validation log to a file.
   *
   * NOTE: When import is run via Drush, this event occurs once at the end of
   * the entire import (after all rows are processed). When run via the
   * admin UI, it occurs at the end of each batch.
   *
   * @param \Drupal\migrate\Event\MigrateImportEvent $event
   *   The import event object.
   */
  public function onMigratePostImport(MigrateImportEvent $event) {

    /** @var \Drupal\migrate\Plugin\MigrationInterface $migration */
    $migration = $event->getMigration();

    if ($migration->getPluginId() !== self::MIGRATION_ID) {
      return;
    }

    // Determine if this is really the end of import, and not the end of a
    // batch (other than the last).
    $import_end = FALSE;

    // Check for a current batch running this migration import.
    if ($batch = $this->getMigrationBatch()) {

      // Determine if all rows have been processed.
      // This post-import event runs at the end of MigrateExecutable::import(),
      // but that's before the batch 'counter' is updated. So 'counter' is
      // currently the total as of the previous batch.
      // In MigrateExecutable::import(), MigrateBatchExecutable::checkStatus()
      // runs for each row processed and increments 'batch_counter'. Thus it's
      // the total of how many rows have been processed in the batch.
      // Add that to 'counter' to get the total as of this batch.
      if (($batch['sets'][$batch['current_set']]['sandbox']['counter']
        + $batch['sets'][$batch['current_set']]['sandbox']['batch_counter'])
        === $batch['sets'][$batch['current_set']]['sandbox']['total']) {

        $import_end = TRUE;

      }

    }
    else {

      // If this migration batch does not exist currently, assume running via
      // Drush.
      $import_end = TRUE;

    }

    if ($import_end) {

      // Save the log out to file.
      \Drupal::service('uwm_import_epic.provider_scheduling_data_validator')->finishLog();

    }

  }

}
