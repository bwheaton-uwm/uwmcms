<?php

namespace Drupal\uwm_puma_migrations\EventSubscriber;

use Drupal\migrate\Event\MigrateEvents;
use Drupal\migrate\Event\MigratePreRowSaveEvent;
use Drupal\migrate_plus\Event\MigrateEvents as MigratePlusEvents;
use Drupal\migrate_plus\Event\MigratePrepareRowEvent;
use Drupal\node\Entity\Node;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Define UwmMigratePumaSubscriber class.
 */
class UwmPumaMigrationsSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {

    // $events[MigrateEvents::PRE_IMPORT] = 'onMigrationPreImport';
    // $events[MigrateEvents::POST_IMPORT] = 'onMigrationPostImport';
    $events[MigrateEvents::PRE_ROW_SAVE] = ['preRowSave'];
    $events[MigrateEvents::MAP_SAVE] = ['mapSave'];
    $events[MigratePlusEvents::PREPARE_ROW] = ['prepareRow'];
    $events[MigrateEvents::POST_ROW_SAVE] = ['postRowSave'];
    return $events;
  }

  /**
   * Prepare a row to be imported.
   *
   * @param Drupal\migrate_plus\Event\MigratePrepareRowEvent $event
   *   The event triggered when preparing a row in a migration.
   */
  public function prepareRow(MigratePrepareRowEvent $event) {
    $migration = $event->getMigration();
    $row = $event->getRow();

    $v = strtolower($row->getSourceProperty('is_internal'));
    if (in_array($v, ['y', 'yes'])) {
      $row->setSourceProperty('is_internal', 1);
    }
    if (in_array($v, ['n', 'no'])) {
      $row->setSourceProperty('is_internal', 0);
    }
  }

  /**
   * React to an item about to be saved.
   *
   * @param \Drupal\migrate\Event\MigratePreRowSaveEvent $event
   *   The pre-save event.
   */
  public function preRowSave(MigratePreRowSaveEvent $event) {
    $migration = $event->getMigration();
    $row = $event->getRow();

    /***
     * Migration name => node field name
     */
    $migrate_field_map = [
      'uwm_edw_board_certs' => 'uwm_edw_board_certs',
      'uwm_edw_puma_basics' => 'uwm_edw_board_certs',
      'uwm_edw_puma_education' => 'uwm_edw_puma_education',
      'uwm_edw_puma_specialities' => 'uwm_edw_puma_specialities',
    ];

    if (in_array($migration->id(), $migrate_field_map)) {
      $destination_nid = $row->getDestinationProperty('nid');

      $node = Node::load($destination_nid);
      if ($node && $node->id()) {
        $field = $migrate_field_map[$migration->id()] ?? NULL;

        $new_value = $row->getDestinationProperty($field);
        self::mergeFieldValues($new_value, $node->$field->value);
        $row->setDestinationProperty($field, $new_value);

        $node->setNewRevision(FALSE);
      }
    }
  }

  /***
   * Prepares value to save for the node.
   *
   * Some CSV imports have multiple rows for the same node, so we merge
   * additional rows for the same node. For example, the PUMA education
   * import may have 3 - 5+ rows for different education experiences.
   * If we are adding additional rows to the same node and field, merge
   * them. Otherwise we replace.
   *
   * @param mixed|null $new_field_value
   *   The new value to save to the node.
   * @param mixed|null $old_field_value
   *   The previously saved node value.
   *
   * @return |null
   *   NULL; modifies by reference.
   *
   * @see uwm_puma_migrations/src/Plugin/migrate/process/UwmSerializeSourceRow.php
   * @TODO: Ensure new value is a [{...}] json array here, rather than where migrate prepares the field.
   */
  private function mergeFieldValues(mixed &$new_field_value = NULL, mixed $old_field_value = NULL) {

    // Nothing to do. Nothing to merge and we keep the new value.
    if (!$old_field_value) {
      return NULL;
    }

    // Nothing to do. The values match; perhaps a migration was
    // rerun with the same source CSV.
    if ($new_field_value == $old_field_value) {
      return NULL;
    }

    // Nothing to do. We were expecting an [{...}] item.
    $new_value_decoded = \GuzzleHttp\json_decode($new_field_value);
    if (!$new_value_decoded || !is_array($new_value_decoded)) {
      return NULL;
    }

    // Nothing to do. We only merge values if new and old are [{...}] arrays.
    $old_value_decoded = \GuzzleHttp\json_decode($old_field_value);
    if (!$old_value_decoded || !is_array($old_value_decoded)) {
      return NULL;
    }

    // Nothing to do. We need a row timestamp to determine a
    // collection from rows.
    $row_timestamp = $new_value_decoded[0]->_date_queried ?? NULL;
    if (!$row_timestamp) {
      return NULL;
    }

    // Nothing to do. The date is different and this must be a new
    // migration run. Keep the new item.
    if ($new_value_decoded[0]->_date_queried != $old_value_decoded[0]->_date_queried) {
      return NULL;
    }

    /***
     * If we made it this far, the migration must be on a new row
     * needing to be added to the field. Create a new value that
     * includes things merged earlier in the run, by merging this row.
     */
    $array = array_unique(array_merge($old_value_decoded, $new_value_decoded), SORT_REGULAR);
    $new_field_value = \GuzzleHttp\json_encode($array);

  }

}
