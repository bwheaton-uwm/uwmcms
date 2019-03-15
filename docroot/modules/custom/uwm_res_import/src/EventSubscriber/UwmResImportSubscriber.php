<?php

namespace Drupal\uwm_res_import\EventSubscriber;

use Drupal\migrate\MigrateSkipRowException;
use Drupal\migrate_plus\Event\MigrateEvents;
use Drupal\migrate_plus\Event\MigratePrepareRowEvent;
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
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[MigrateEvents::PREPARE_ROW] = ['prepareRow'];
    return $events;
  }

}
