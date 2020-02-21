<?php

namespace Drupal\uwmcs_ecare_scheduling\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\migrate\Event\MigrateEvents;
use Drupal\migrate\Event\MigrateImportEvent;
use Drupal\migrate\Event\MigratePreRowSaveEvent;
use Drupal\Core\TempStore\PrivateTempStoreFactory;
use Drupal\Core\Messenger\MessengerInterface;
use Psr\Log\LoggerInterface;
use Drupal\Core\Mail\MailManagerInterface;

/**
 * Class ResProviderMigrationSubscriber.
 *
 * Subscribes to events when running the import of providers from Reservoir.
 * Validates whether any visit type IDs being imported on providers are missing
 * corresponding display name/description stored in Visit Type Labels terms.
 *
 * @TODO: 2020-02-21, Tory:
 * Once Reservoir is removed and confirmed migrations are no longer needed,
 * this entire class and the corresponding mail implementation in
 * uwmcs_ecare_scheduling_mail() can be deleted.
 */
class ResProviderMigrationSubscriber implements EventSubscriberInterface {

  /**
   * ID of the migration we want to interact with.
   */
  const MIGRATION_ID = 'uwm_res_providers_import_providers';

  /**
   * The private temp store factory service.
   *
   * @var \Drupal\Core\TempStore\PrivateTempStoreFactory
   */
  protected $privateTempStoreFactory;

  /**
   * The private temp store collection used by this service.
   *
   * @var \Drupal\Core\TempStore\PrivateTempStore
   */
  protected $privateTempStore;

  /**
   * The messenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  protected $messenger;

  /**
   * The logger channel for service.
   *
   * @var \Psr\Log\LoggerInterface
   */
  protected $logger;

  /**
   * The mail manager service.
   *
   * @var \Drupal\Core\Mail\MailManagerInterface
   */
  protected $mailManager;

  /**
   * Local store of all visit types' names and descriptions.
   *
   * @var array
   */
  protected $visitTypes;

  /**
   * Constructor.
   *
   * @param \Drupal\Core\TempStore\PrivateTempStoreFactory $private_temp_store_factory
   *   The private temp store factory service.
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger service.
   * @param \Psr\Log\LoggerInterface $logger
   *   The logger channel for service.
   * @param \Drupal\Core\Mail\MailManagerInterface $mail_manager
   *   The mail manager service.
   */
  public function __construct(PrivateTempStoreFactory $private_temp_store_factory, MessengerInterface $messenger, LoggerInterface $logger, MailManagerInterface $mail_manager) {

    $this->messenger = $messenger;
    $this->logger = $logger;
    $this->mailManager = $mail_manager;

    // In a batch situation there are multiple requests, so this object is
    // constructed on each request. Use a private temp store to record our
    // validation log across multiple requests.
    $this->privateTempStoreFactory = $private_temp_store_factory;

    // Create or access our temp store collection.
    $this->privateTempStore = $this->privateTempStoreFactory->get('uwmcs_ecare_scheduling.provider_import_validate');

    // Load all Visit Type Labels terms which store name and description for
    // visit type IDs.
    $this->visitTypes = _uwmcs_ecare_scheduling_load_visit_type_terms();

  }

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
   * At the beginning of the entire import, initialize the log in temp store.
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

    // Determine if this import is running via admin UI using batches or
    // command line via Drush.
    $import_beginning = NULL;

    if ($batch = $this->getMigrationBatch()) {

      // Check if it's the beginning of the entire import, and not the beginning
      // of a batch after the first. The counter value tracks total number of
      // rows processed across all batches.
      if ($batch['sets'][$batch['current_set']]['sandbox']['counter'] === 0) {

        $import_beginning = 'batch';

      }

    }
    else {

      // If this migration batch does not exist currently, assume running via
      // Drush.
      $import_beginning = 'drush';

    }

    // If at the beginning, initialize the temp store values.
    if (!empty($import_beginning)) {

      // Set (or reset) array to collect visit type IDs found to be missing
      // name/description.
      $this->privateTempStore->set('visit_types_missing', []);

      // Denote if running via admin UI in batches, to handle reporting
      // differently.
      $this->privateTempStore->set('ui_batch', ($import_beginning === 'batch'));

    }

  }

  /**
   * Run handling on migrate pre-row-save event.
   *
   * Check if provider has any visit type IDs that don't have a taxonomy term
   * storing the visit type name and description.
   *
   * @param \Drupal\migrate\Event\MigratePreRowSaveEvent $event
   *   The import event object.
   */
  public function onMigratePreRowSave(MigratePreRowSaveEvent $event) {

    /** @var \Drupal\migrate\Plugin\MigrationInterface $migration */
    $migration = $event->getMigration();

    if ($migration->getPluginId() !== self::MIGRATION_ID) {
      return;
    }

    /** @var \Drupal\migrate\Row $row */
    $row = $event->getRow();

    /*
     * Retrieve values to be saved to node.
     * Example:
     * [
     *   ...
     *   "field_res_visit_type_id" => array:3 [
     *     0 => "1234"
     *     1 => "5432"
     *  ]
     *   ...
     * ]
     */
    $dest_values = $row->getDestination();

    if (!empty($dest_values['field_res_visit_type_id'])) {

      foreach ($dest_values['field_res_visit_type_id'] as $visit_type_id) {

        // Check if the visit type ID about to be saved to a provider does not
        // have a corresponding Visit Type Labels term, or that term is missing
        // the name or description value.
        if (empty($this->visitTypes[$visit_type_id]) || empty($this->visitTypes[$visit_type_id]['name']) || empty($this->visitTypes[$visit_type_id]['description'])) {

          // Record this visit type ID, if not already.
          $missing = $this->privateTempStore->get('visit_types_missing');

          if (!in_array($visit_type_id, $missing)) {

            $missing[] = $visit_type_id;
            $this->privateTempStore->set('visit_types_missing', $missing);

            // If running via admin UI in batches, report this now.
            // @see self::onMigratePostImport()
            if ($this->privateTempStore->get('ui_batch')) {

              $this->reportMissingVisitTypes([
                $visit_type_id,
              ]);

            }

          }

        }

      }

    }

  }

  /**
   * Run handling on migrate post-import event.
   *
   * At the end of the entire import, output/record any missing visit types.
   *
   * NOTE: When import is run via Drush, this event occurs once at the end of
   * the entire import (after all rows are processed). When run via the
   * admin UI, it occurs at the end of each batch.
   * The batch method often does not process all source items, making it
   * difficult/impossible to determine when at the end of the last batch.
   * Thus if currently running via admin UI batches, we do NOT handle this event
   * as happening at the end of the import (i.e. do not do any reporting).
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

    // Since we are not reliably detecting the end of the entire import when
    // running via batches, check if running NOT using batches. That implies
    // running via Drush, in which case this event occurs only at the end of the
    // entire import - allowing to do a final report.
    //
    // Note: do not clear the temp store values, because if running batches we
    // need to maintain visit type IDs across batches. They are cleared when
    // the beginning of entire import is detected.
    // @see self::onMigratePreImport()
    if (!$this->getMigrationBatch()) {

      $missing = $this->privateTempStore->get('visit_types_missing');

      if (!empty($missing)) {

        $this->reportMissingVisitTypes($missing);

      }
      else {

        // Output success message.
        // (Unfortunately there's no good way to output this message if running
        // via batches, because each batch may or may not have $missing empty.)
        $this->messenger->addMessage(t("Providers import from Reservoir - all visit type IDs on imported providers have their visit type name and description."));

      }

    }

  }

  /**
   * Report visit type ID(s) that are missing name/description.
   *
   * This reports in 3 ways:
   * - output message to UI or console
   * - entry in Drupal database log, with type 'provider_visit_types'
   * - notification email.
   *
   * @param array $missing
   *   Array of visit type IDs to report.
   */
  protected function reportMissingVisitTypes(array $missing) {

    if (empty($missing)) {
      return;
    }

    // Output message to page (if run via UI) or console (if via Drush).
    $this->messenger->addMessage(t("Providers import from Reservoir - the following visit type IDs were imported on providers, but their visit type names/descriptions were not found:<br/>
    %visit_type_ids_missing<br/>
    <a href=\"/admin/structure/taxonomy/manage/visit_type_labels/overview\" target=\"_blank\">Add them here.</a>", [
      '%visit_type_ids_missing' => implode(', ', $missing),
    ]), 'error');

    // Record message in the Drupal database log.
    // The 'Type' in the Recent Log Messages page is 'provider_visit_types'.
    // Note: The logger categorizes messages into levels.
    // @see https://api.drupal.org/api/drupal/vendor%21psr%21log%21Psr%21Log%21LogLevel.php/class/LogLevel/8.6.x
    //
    // TODO: if run via Drush, this also outputs to the console, which is
    // duplicative of the message set above, but does not have HTML stripped
    // and reformatted. This is because (?) Drush implements a logger that
    // gets registered.
    $this->logger->error("Providers import from Reservoir - the following visit type IDs were imported on providers, but their visit type names/descriptions were not found:<br/>
    %visit_type_ids_missing<br/>
    <a href=\"/admin/structure/taxonomy/manage/visit_type_labels/overview\" target=\"_blank\">Add them here.</a>", [
      '%visit_type_ids_missing' => implode(', ', $missing),
    ]);

    // Send email notification, since this import runs regularly and
    // automatically, and admins are unlikely to see the output/log message.
    // Set the 'to' address in the hook_mail() implementation.
    // Use the site default language for now.
    // @see uwmcs_ecare_scheduling_mail()
    $this->mailManager->mail(
      'uwmcs_ecare_scheduling',
      'res_validate_provider_visit_types',
      NULL,
      NULL,
      [
        'visit_type_ids_missing' => $missing,
        'url_taxonomy_vocab' => '/admin/structure/taxonomy/manage/visit_type_labels/overview',
      ]
    );

  }

}
