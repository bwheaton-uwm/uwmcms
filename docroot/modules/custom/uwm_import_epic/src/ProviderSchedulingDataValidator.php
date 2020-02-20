<?php

namespace Drupal\uwm_import_epic;

use Drupal\Core\File\FileSystemInterface;
use Drupal\Core\TempStore\PrivateTempStoreFactory;
use Drupal\Core\Messenger\MessengerInterface;
use Psr\Log\LoggerInterface;
use Drupal\Core\Mail\MailManagerInterface;

/**
 * Class ProviderSchedulingDataValidator.
 *
 * Validate for illogical/undesirable data in provider scheduling values and
 * generate log of issues found. This is utilized when importing this data from
 * Epic export.
 *
 * @see ProviderSchedulingMigrationSubscriber
 */
class ProviderSchedulingDataValidator {

  /**
   * URI of directory to store validation logs.
   */
  const LOG_DIRECTORY = 'private://provider-scheduling-data-validation-logs';

  /**
   * The file system service.
   *
   * @var \Drupal\Core\File\FileSystemInterface
   */
  protected $fileSystem;

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
   * Whether to store log status and messages in database temp store.
   *
   * @var bool
   */
  private $useTempStore;

  /**
   * Whether currently collecting messages for a single log.
   *
   * @var bool
   */
  private $isLogging;

  /**
   * Messages for current log, if not using temp store.
   *
   * @var array
   */
  private $log;

  /**
   * Local store of all visit types' names and descriptions.
   *
   * @var array
   */
  protected $visitTypes;

  /**
   * Visit type IDs for which name/description is not found, for current log.
   *
   * @var array
   */
  protected $visitTypesMissingNameDesc;

  /**
   * The messenger service.
   *
   * @var \Drupal\Core\Messenger\MessengerInterface
   */
  private $messenger;

  /**
   * The logger channel for service.
   *
   * @var \Psr\Log\LoggerInterface
   */
  private $logger;

  /**
   * The mail manager service.
   *
   * @var \Drupal\Core\Mail\MailManagerInterface
   */
  protected $mailManager;

  /**
   * Constructor.
   *
   * @param \Drupal\Core\File\FileSystemInterface $file_system
   *   The file system service.
   * @param \Drupal\Core\TempStore\PrivateTempStoreFactory $private_temp_store_factory
   *   The private temp store factory service.
   * @param \Drupal\Core\Messenger\MessengerInterface $messenger
   *   The messenger service.
   * @param \Psr\Log\LoggerInterface $logger
   *   The logger channel for service.
   * @param \Drupal\Core\Mail\MailManagerInterface $mail_manager
   *   The mail manager service.
   */
  public function __construct(FileSystemInterface $file_system, PrivateTempStoreFactory $private_temp_store_factory, MessengerInterface $messenger, LoggerInterface $logger, MailManagerInterface $mail_manager) {

    $this->fileSystem = $file_system;
    $this->messenger = $messenger;
    $this->logger = $logger;
    $this->mailManager = $mail_manager;

    // In a batch situation there are multiple requests, so this object is
    // constructed on each request. To determine if there's a log in progress
    // from a previous request, check for data in temp store.
    $this->privateTempStoreFactory = $private_temp_store_factory;

    // Create or access our temp store collection.
    $this->privateTempStore = $this->privateTempStoreFactory->get('uwm_import_epic.scheduling_data_validator');

    // If the temp store logging status value is present and true, we already
    // have a log in progress.
    // (If a key/value is not set in the store, this method returns NULL.)
    if ($this->privateTempStore->get('is_logging')) {

      $this->useTempStore = TRUE;

    }
    else {

      $this->useTempStore = FALSE;
      $this->setIsLogging(FALSE);

    }

    // Load all Visit Type Labels terms which store name and description for
    // visit type IDs.
    $this->visitTypes = _uwmcs_ecare_scheduling_load_visit_type_terms();

  }

  /**
   * Output and/or record a status (or error) message about validation process.
   *
   * This wraps various ways of informing the user:
   * - display message on the page (if executing via UI)
   * - output message to console (if executing via Drush)
   * - record to Drupal "watchdog" database log.
   *
   * @param string $type
   *   Message type: 'status' or 'error'.
   * @param string $message
   *   Message text.
   */
  protected function outputStatus($type, $message) {

    // If run via UI - output message to user on the page (after batches).
    // If run via Drush - output nicely formatted message (HTML stripped) to
    // console (at end of any messages from migrate).
    $this->messenger->addMessage(t("[Provider Scheduling Data Validator] @message", [
      '@message' => $message,
    ]), $type);

    // Record message in the Drupal database log, for both via UI and Drush.
    // The 'Type' in the Recent Log Messages page is
    // 'provider_scheduling_validator'.
    // Note: The logger categorizes messages into levels. Use one of two that
    // correspond to the displayed message: 'notice' or 'error'.
    // @see https://api.drupal.org/api/drupal/vendor%21psr%21log%21Psr%21Log%21LogLevel.php/class/LogLevel/8.6.x
    //
    // TODO: if run via Drush, this also outputs to the console, which is
    // duplicative of the message set above, but does not have HTML stripped
    // and reformatted. This is because (?) Drush implements a logger that gets
    // registered.
    $level = $type;
    if ($type === 'status') {
      $level = 'notice';
    }

    $this->logger->log($level, $message);

  }

  /**
   * Try to create directory for saving log files in the private file system.
   *
   * @return bool
   *   Whether the directory (now) exists and is writeable.
   */
  protected function initLogDirectory() {

    /*
     * TODO: update this on 8.7.x+
     *
     * @code
     * $this->fileSystem->prepareDirectory(self::LOG_DIRECTORY,
     * FileSystemInterface::CREATE_DIRECTORY |
     * FileSystemInterface::MODIFY_PERMISSIONS);
     * @endcode
     *
     * @see https://www.drupal.org/node/3006851
     */
    // The $directory arg is passed by reference; not sure why.
    $directory = self::LOG_DIRECTORY;
    return file_prepare_directory($directory, FILE_CREATE_DIRECTORY | FILE_MODIFY_PERMISSIONS);

  }

  /**
   * Getter for whether a log is currently in progress.
   *
   * @return bool
   *   The logging status.
   */
  private function getIsLogging() {

    if ($this->useTempStore) {

      return $this->privateTempStore->get('is_logging');

    }
    else {

      return $this->isLogging;

    }

  }

  /**
   * Setter for whether a log is currently in progress.
   *
   * @param bool $is_logging
   *   Status value to set.
   */
  private function setIsLogging($is_logging) {

    if ($this->useTempStore) {

      $this->privateTempStore->set('is_logging', $is_logging);

    }
    else {

      $this->isLogging = $is_logging;

    }

  }

  /**
   * Add a single message/line to the current log.
   *
   * @param array $message
   *   Message values, to be joined with commas to format as a CSV line.
   */
  protected function addLogMessage(array $message) {

    $message = implode(",", $message);

    if ($this->useTempStore) {

      // Store each message in its own key/value entry, tracking the count
      // for convenience. This avoids storing a potentially huge array of all
      // messages in a single value.
      $count = $this->privateTempStore->get('log_count');
      $count++;
      $this->privateTempStore->set('log_' . $count, $message);
      $this->privateTempStore->set('log_count', $count);

    }
    else {

      // Store simply in this object.
      $this->log[] = $message;

    }

  }

  /**
   * Retrieve all current log messsages.
   *
   * @return array
   *   The header and all messages, each formatted as comma-separated values.
   */
  protected function getLog() {

    if ($this->useTempStore) {

      $log = [];

      // Retrieve each message value.
      for ($c = 1; $c <= $this->privateTempStore->get('log_count'); $c++) {

        $log[] = $this->privateTempStore->get('log_' . $c);

      }

      return $log;

    }
    else {

      return $this->log;

    }

  }

  /**
   * Get visit types missing name/description, thus far in current log.
   *
   * @return array
   *   Array of visit type IDs.
   */
  protected function getVisitTypesMissingNameDesc() {

    if ($this->useTempStore) {

      return $this->privateTempStore->get('visit_types_missing_name_desc');

    }
    else {

      return $this->visitTypesMissingNameDesc;

    }

  }

  /**
   * Check if visit type is missing name/description; add to current log if so.
   *
   * @param int $visit_type_id
   *   The visit type ID.
   *
   * @return bool
   *   Whether the visit type ID is missing name/description stored as Visit
   *   Type Labels taxonomy term.
   */
  protected function checkAddVisitTypeMissingNameDesc($visit_type_id) {

    if (in_array($visit_type_id, $this->getVisitTypesMissingNameDesc())) {

      return TRUE;

    }
    elseif (empty($this->visitTypes[$visit_type_id]) || empty($this->visitTypes[$visit_type_id]['name']) || empty($this->visitTypes[$visit_type_id]['description'])) {

      // Record in the current log, adding each ID only once.
      if ($this->useTempStore) {

        $missing = $this->privateTempStore->get('visit_types_missing_name_desc');

        if (!in_array($visit_type_id, $missing)) {

          $missing[] = $visit_type_id;
          $this->privateTempStore->set('visit_types_missing_name_desc', $missing);

        }

      }
      else {

        if (!in_array($visit_type_id, $this->visitTypesMissingNameDesc)) {

          $this->visitTypesMissingNameDesc[] = $visit_type_id;

        }

      }

      return TRUE;

    }

    return FALSE;

  }

  /**
   * Remove all current log messages and reset status.
   *
   * This is called after the import is completed and all log messages have been
   * written to file. If using the temp store, it is particularly important to
   * remove this data - which is no longer needed - from the database and ensure
   * it does not exist when the next log is initialized.
   *
   * Note: this is called from finishLog() regardless of what has happened.
   * There may be no log or a partial log, so it cleans cautiously but fully.
   */
  protected function reset() {

    if ($this->useTempStore) {

      $count = $this->privateTempStore->get('log_count');

      if (!empty($count) && is_numeric($count)) {

        for ($c = 1; $c <= (int) $count; $c++) {

          $this->privateTempStore->delete('log_' . $c);

        }

      }

      $this->privateTempStore->delete('log_count');
      $this->privateTempStore->delete('visit_types_missing_name_desc');
      $this->privateTempStore->set('is_logging', FALSE);

    }
    else {

      $this->log = [];
      $this->visitTypesMissingNameDesc = [];
      $this->isLogging = FALSE;

    }

  }

  /**
   * Initialize a new log.
   *
   * @param bool $use_temp_store
   *   Whether to store the log status and messages in the 'temp store' in the
   *   database. When the import is run via the admin UI, it uses batches, each
   *   of which is a separate request; storing in the database persists values
   *   across these requests. (It is not necessary for running via Drush.)
   */
  public function initLog($use_temp_store = FALSE) {

    if ($this->getIsLogging()) {

      $this->outputStatus('error', t("Error initializing a log: another log is already in progress."));

    }
    else {
      if ($this->initLogDirectory()) {

        $this->outputStatus('status', t("Successfully initialized a log."));

        // Determine if using the temp store to store logging values.
        $this->useTempStore = $use_temp_store;

        // Initialize logging values.
        if ($this->useTempStore) {

          $this->privateTempStore->set('log_count', 0);
          $this->privateTempStore->set('visit_types_missing_name_desc', []);

        }
        else {

          $this->log = [];
          $this->visitTypesMissingNameDesc = [];

        }

        $this->setIsLogging(TRUE);

        // Add the CSV header row.
        $this->addLogMessage([
          'Drupal Node ID',
          'Provider NPI',
          'Provider Name',
          'Provider Epic ID',
          'Issue Type',
          'Message',
        ]);

      }
      else {

        $this->outputStatus('error', t('Error initializing a log: could not create file directory to store logs or could not make the directory writeable. Directory attempted: <code>@directory</code>.<br/>
        Check for additional error messages from the file system in the <a href=":url_db_log" target="_blank">Drupal log</a>.', [
          '@directory' => self::LOG_DIRECTORY,
          ':url_db_log' => '/admin/reports/dblog',
        ]));

      }
    }

  }

  /**
   * Retrieve all current log messages and write out to CSV file, then reset.
   */
  public function finishLog() {

    if (!$this->getIsLogging()) {

      $this->outputStatus('error', t("Error finishing log: there is no log in progress."));

    }
    else {

      $log = $this->getLog();

      // If there are no log messages, the count is 1 - just the CSV header.
      if (count($log) === 1) {

        $this->outputStatus('status', t("Finished validation. All data is good! Log file was not generated."));

      }
      else {

        // The log array is one message per line, formatted for CSV; combine all
        // lines to a string to write to file.
        $log_string = implode("\n", $log);

        // Include the current date/time in the filename, ensuring output in
        // our timezone.
        $datetime = new \DateTime('now', new \DateTimeZone('America/Los_Angeles'));
        $filepath = self::LOG_DIRECTORY . '/uwm-provider-scheduling-data-validation--' . $datetime->format('Y-m-d--H.i.s-T') . '.csv';

        /*
         * TODO: update this on 8.7.x+
         *
         * @code
         * $this->fileSystem->saveData($log_string, $filepath,
         * FileSystemInterface::EXISTS_REPLACE);
         * @endcode
         *
         * This version will need to catch(FileException), instead of checking
         * for the save returning false.
         *
         * @see https://www.drupal.org/node/3006851
         */
        if (file_unmanaged_save_data($log_string, $filepath, FILE_EXISTS_REPLACE)) {

          // Note: this links to an expected Directory Listing node for the
          // private files directory where log files are saved. This node
          // type is provided by the Filebrowser module to provide UI access to
          // files in the file system.
          $url_logs_directory_node = '/admin/files/private-files-provider-scheduling-data-validation-logs';

          $this->outputStatus('status', t('Finished validation.<br/>
          Log file saved at: <code>@filepath</code>.<br/>
          <a href=":url_logs_directory_node" target="_blank">See all logs here.</a>', [
            '@filepath' => $filepath,
            ':url_logs_directory_node' => $url_logs_directory_node,
          ]));

          // Send email notification with link to logs admin page.
          // Set 'to' address and language in the hook_mail() implementation.
          // @see uwmcs_ecare_scheduling_mail()
          $this->mailManager->mail(
            'uwmcs_ecare_scheduling',
            'provider_scheduling_data_validation',
            NULL,
            NULL,
            [
              'log_filepath' => $filepath,
              'url_logs_directory_node' => $url_logs_directory_node,
            ]
          );

        }
        else {

          // TODO: link to a UI to re-run validation separately from migration,
          // if implemented.
          $this->outputStatus('error', t('Error generating log file, intended to be saved at: <code>@filepath</code>.<br/>
          Check for additional error messages from the file system in the <a href=":url_db_log" target="_blank">Drupal log</a>.', [
            '@filepath' => $filepath,
            ':url_db_log' => '/admin/reports/dblog',
          ]));

          // Send email notification of log file generation failure.
          $this->mailManager->mail(
            'uwmcs_ecare_scheduling',
            'provider_scheduling_data_validation_log_failure',
            NULL,
            NULL,
            [
              'log_filepath' => $filepath,
            ]
          );

        }

        // Provide separate message for visit types missing name/description,
        // which are not tracked in the log.
        $visit_types_missing = $this->getVisitTypesMissingNameDesc();
        if (!empty($visit_types_missing)) {

          $this->outputStatus('error', t('The following visit type IDs were found on providers, but their display names/descriptions were not found:<br/>
          %visit_type_ids_missing<br/>
          <a href=":url_taxonomy_vocab" target="_blank">Add them here.</a>', [
            '%visit_type_ids_missing' => implode(', ', $visit_types_missing),
            ':url_taxonomy_vocab' => '/admin/structure/taxonomy/manage/visit_type_labels/overview',
          ]));

          $this->mailManager->mail(
            'uwmcs_ecare_scheduling',
            'provider_scheduling_data_validation_visit_types',
            NULL,
            NULL,
            [
              'visit_type_ids_missing' => $visit_types_missing,
              'url_taxonomy_vocab' => '/admin/structure/taxonomy/manage/visit_type_labels/overview',
            ]
          );

        }

      }

    }

    // No matter what happened, reset to ensure fresh log data next time.
    $this->reset();

  }

  /**
   * Validate scheduling values for a single provider and log all issues found.
   *
   * @param array $identifiers
   *   Values that identify the provider whose data is to be validated.
   * @param array $values
   *   The provider's most-current scheduling data values. If running during an
   *   import, these are the values about to be imported.
   * @param array $existing
   *   The provider's existing scheduling data values. If running during an
   *   import, these are the existing node field values.
   *
   * @throws \InvalidArgumentException
   *   If the identifiers array or the values array is missing any required
   *   values, throw exception because the calling code must provide these
   *   values to accurately validate.
   *
   * @return bool|array
   *   TRUE if no issues found. Otherwise, the log array of issues found.
   */
  public function validateOne(array $identifiers, array $values, array $existing = NULL) {

    // Check that array arguments contain all necessary values.
    $diff_identifiers = array_diff([
      'nid',
      'npi',
      'name',
    ], array_keys($identifiers));

    if (!empty($diff_identifiers)) {
      throw new \InvalidArgumentException("\$identifiers array missing some values: " . implode(" ", $diff_identifiers));
    }

    $diff_values = array_diff([
      'accepting_returning',
      'accepting_new',
      'epic_id',
      'direct_enabled',
      'open_enabled',
      'visit_type_ids',
    ], array_keys($values));

    if (!empty($diff_values)) {
      throw new \InvalidArgumentException("\$values array missing some values: " . implode(" ", $diff_values));
    }

    // Collect messages by type of data issue. Case IDs (a, b, c, ...) denoted
    // in Wrike tasks are marked here.
    // @see https://www.wrike.com/open.htm?id=418746142
    // @see https://www.wrike.com/open.htm?id=417535639
    // @see https://www.wrike.com/open.htm?id=411521672
    $log = [
      'incomplete_no' => [],
      'incomplete_partial' => [],
      'error' => [],
      'verify' => [],
      'auto_change' => [],
      'visit_types' => [],
    ];

    // Incomplete source data cases: when the source row is missing value(s)
    // necessary to import to this provider.
    // (a)
    if (empty($identifiers['npi'])) {

      $log['incomplete_no']['npi'] = "Missing source value: provider NPI";

    }

    // (b)
    // Note: if the NPI is missing, it'll never be mapped to a node. Thus, it's
    // not helpful to log the missing node when we've already logged that the
    // NPI is missing.
    if (!empty($identifiers['npi']) && empty($identifiers['nid'])) {

      $log['incomplete_no']['nid'] = "No Drupal node found for provider NPI";

    }

    // (c)
    // Since this value should be a boolean, check specifically for NULL to
    // check if it's missing.
    if (is_null($values['direct_enabled'])) {

      $log['incomplete_partial']['direct_enabled'] = "Missing source value: is enabled for direct scheduling (must be 'Yes' or 'No')";

      // For remaining validation, use existing value if present.
      // This assumes new values will take effect (e.g. be imported) and for
      // missing ones, existing values will persist, so the combination should
      // be validated here.
      if (isset($existing['direct_enabled'])) {
        $values['direct_enabled'] = $existing['direct_enabled'];
      }

    }

    // (d)
    // Since this value should be a boolean, check specifically for NULL to
    // check if it's missing.
    if (is_null($values['open_enabled'])) {

      $log['incomplete_partial']['open_enabled'] = "Missing source value: is enabled for open scheduling (must be 'Yes' or 'No')";

      // For remaining validation, use existing value if present.
      // This assumes new values will take effect (e.g. be imported) and for
      // missing ones, existing values will persist, so the combination should
      // be validated here.
      if (isset($existing['open_enabled'])) {
        $values['open_enabled'] = $existing['open_enabled'];
      }

    }

    // Error cases: "bad data" coming from Epic, i.e. data that is by definition
    // illogical.
    // (e)
    if ($values['open_enabled'] === TRUE && empty($values['epic_id'])) {

      $log['error'][] = "Open scheduling is enabled, but missing Epic ID";

    }

    // (f)
    if ($values['direct_enabled'] === TRUE && empty($values['epic_id'])) {

      $log['error'][] = "Direct scheduling is enabled, but missing Epic ID";

    }

    // (g)
    if ($values['open_enabled'] === TRUE && empty($values['visit_type_ids'])) {

      $log['error'][] = "Open scheduling is enabled, but missing visit types";

    }

    // Verification cases: potential inconsistencies between Epic data and
    // human-managed data. They are likely, but not always, incorrect, and thus
    // should be manually checked.
    // Automatic change cases: a change in the Epic value should automatically
    // change an otherwise human-managed value. These are recorded here, but
    // the change implementation is left up to the caller.
    // (i, k)
    if ($values['open_enabled'] === TRUE && $values['accepting_new'] === FALSE) {

      // Automatic logic:
      // IF provider is now being enabled for open scheduling,
      // THEN denote to set provider to accepting new patients.
      //
      // Note: compare existing value via `!== TRUE` to account for both a
      // FALSE field value and an empty field value which takes effect
      // as FALSE.
      if (!empty($existing) && $existing['open_enabled'] !== TRUE) {

        $log['auto_change']['accepting_new'] = TRUE;

        // Also update this value to be used for the remaining validation.
        $values['accepting_new'] = TRUE;

      }
      else {

        // If they were already enabled for open scheduling, but are not
        // accepting - that is an existing state that may be for some reason,
        // so we do not change 'accepting' status but log it for verification.
        $log['verify'][] = "Open scheduling is enabled, but not accepting new patients";

      }

    }

    // (l)
    if ($values['open_enabled'] === FALSE && !empty($existing) && $existing['open_enabled'] === TRUE && $values['accepting_new'] === TRUE) {

      $log['verify'][] = "CHANGED: Open scheduling has been turned off; verify if provider is still accepting new patients via phone calls";

    }

    // (j, m)
    if ($values['direct_enabled'] === TRUE && $values['accepting_returning'] === FALSE) {

      // Automatic logic:
      // IF provider is now being enabled for direct scheduling,
      // THEN denote to set provider to accepting returning patients.
      //
      // Note: compare existing value via `!== TRUE` to account for both a
      // FALSE field value and an empty field value which takes effect
      // as FALSE.
      if (!empty($existing) && $existing['direct_enabled'] !== TRUE) {

        $log['auto_change']['accepting_returning'] = TRUE;

        // Also update this value to be used for the remaining validation.
        $values['accepting_returning'] = TRUE;

      }
      else {

        // If they were already enabled for direct scheduling, but are not
        // accepting - that is an existing state that may be for some reason,
        // so we do not change 'accepting' status but log it for verification.
        $log['verify'][] = "Direct scheduling is enabled, but not accepting returning patients";

      }

    }

    // (n)
    if ($values['direct_enabled'] === FALSE && !empty($existing) && $existing['direct_enabled'] === TRUE && $values['accepting_returning'] === TRUE) {

      $log['verify'][] = "CHANGED: Direct scheduling has been turned off; verify if provider is still accepting returning patients via phone calls";

    }

    // (h)
    // Note: run this check after the automatic-change cases, which may have
    // updated these values.
    if ($values['accepting_new'] === TRUE && $values['accepting_returning'] === FALSE) {

      $log['verify'][] = "Accepting new patients, but not accepting returning patients";

    }

    // (o)
    // Log missing Visit Type Labels term or display name/description field
    // value on term with same message for simplicity, because all are managed
    // in one place.
    if (!empty($values['visit_type_ids'])) {
      foreach ($values['visit_type_ids'] as $visit_type_id) {

        if ($this->checkAddVisitTypeMissingNameDesc($visit_type_id)) {

          $log['visit_types'][] = "Visit type ID " . $visit_type_id . " present, but display name/description not found in Visit Type Labels taxonomy";

        }

      }
    }

    // Store each message in the current log, including values to identify the
    // provider with the issue, type of issue, and specific message.
    if (!empty($log)) {

      // Note: skip logging the auto-change cases - they are correct situations
      // so do not need to be reported/verified.
      $type_labels = [
        'incomplete_no' => "Not imported",
        'incomplete_partial' => "Partially imported",
        'error' => "Logic error",
        'verify' => "Verify",
        'visit_types' => "Visit type missing name/desc",
      ];

      foreach ($log as $type => $messages) {
        if (empty($type_labels[$type])) {
          continue;
        }

        foreach ($messages as $message) {

          // Enclose values that may contain commas in quotes.
          $this->addLogMessage([
            $identifiers['nid'],
            $identifiers['npi'],
            '"' . $identifiers['name'] . '"',
            (string) $values['epic_id'],
            $type_labels[$type],
            '"' . $message . '"',
          ]);

        }
      }

      // Return the log for caller to use however.
      return $log;

    }
    else {

      // Indicate that no issues were found.
      return TRUE;

    }

  }

}
