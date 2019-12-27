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
   * ID of the batch that's currently running this migration via the UI, if any.
   *
   * @var int
   */
  protected $batchId = NULL;

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

    // If at the beginning, initialize the temp store value for recording
    // missing visit types.
    if (!empty($import_beginning)) {

      $this->privateTempStore->set('visit_types_missing', []);

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

      $missing = $this->privateTempStore->get('visit_types_missing');

      if (!empty($missing)) {

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

        // TODO: send only on prod / configurable in admin UI / env indicator
        //
        // Send email notification, since this import runs regularly and
        // automatically, and admins are unlikely to see the output/log message.
        // Set the 'to' address in the hook_mail() implementation.
        // Use the site default language for now.
        // @see uwmcs_ecare_scheduling_mail()
        $this->mailManager->mail(
          'uwmcs_ecare_scheduling',
          'validate_provider_visit_types',
          NULL,
          \Drupal::languageManager()->getDefaultLanguage()->getId(),
          [
            'visit_type_ids_missing' => $missing,
          ]
        );

      }
      else {

        // Output success message.
        $this->messenger->addMessage(t("Providers import from Reservoir - all visit type IDs on imported providers have their visit type name and description."));

      }

      // Clear the temp store value now that we've logged it.
      $this->privateTempStore->delete('visit_types_missing');

    }

  }

}
