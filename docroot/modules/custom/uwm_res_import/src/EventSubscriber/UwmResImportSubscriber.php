<?php

namespace Drupal\uwm_res_import\EventSubscriber;

use Drupal\migrate\Event\MigrateEvents;
use Drupal\migrate_plus\Event\MigrateEvents as MigratePlusEvents;
use Drupal\migrate\MigrateSkipRowException;
use Drupal\migrate_plus\Event\MigratePrepareRowEvent;
use Drupal\migrate\Event\MigratePreRowSaveEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Define UwmResImportSubscriber class.
 */
class UwmResImportSubscriber implements EventSubscriberInterface {

  /**
   * Prepare a row to be imported.
   *
   * @param Drupal\migrate_plus\Event\MigratePrepareRowEvent $event
   *   The event triggered when preparing a row in a migration.
   */
  public function prepareRow(MigratePrepareRowEvent $event) {

    /** @var \Drupal\migrate\Row $row */
    $row = $event->getRow();

    /** @var \Drupal\migrate\Plugin\MigrationInterface $migration */
    $migration = $event->getMigration();

    if (self::useHighwatermark() !== TRUE) {
      return;
    }

    // Get the timestamp for when the import was last run.
    $migrate_last_imported_store = \Drupal::keyValue('migrate_last_imported');
    $last_imported = $migrate_last_imported_store->get($migration->id(), FALSE);
    $last_updated = $row->getSourceProperty('changed');

    // If the item in this row was not updated after the last import,
    // skip processing of the row.
    if ($last_imported && $last_updated) {
      $last_imported = $last_imported / 1000;
      if ($last_imported >= $last_updated) {
        throw new MigrateSkipRowException();
      }
    }

  }

  /**
   * React to an item about to be imported.
   *
   * @param \Drupal\migrate\Event\MigratePreRowSaveEvent $event
   *   The pre-save event.
   */
  public function preRowSave(MigratePreRowSaveEvent $event) {

    /** @var \Drupal\migrate\Row $row */
    // $row = $event->getRow();
    /** @var \Drupal\migrate\Plugin\MigrationInterface $migration */
    // $migration = $event->getMigration();
    // $id_map = $event->getRow()->getIdMap();
    // if (!empty($id_map['destid1'])) {
    // $this->preExistingItem = TRUE;
    // }
    // else {
    // $this->preExistingItem = FALSE;
    // }.
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[MigratePlusEvents::PREPARE_ROW] = ['prepareRow'];
    $events[MigrateEvents::PRE_ROW_SAVE] = ['preRowSave'];
    return $events;
  }

  /**
   * Determine if to cache or highwatermark should be disabled.
   *
   * @return bool
   *   Whether to preserve the highwater mark.
   */
  private static function useHighwatermark() {

    $config = \Drupal::config('uwm_res_import.settings');
    $setting = $config->get('use_highwatermark');

    if (isset($setting)) {
      return (bool) $setting;
    }

    return TRUE;

  }

}
