<?php

namespace Drupal\uwm_migrations\Plugin\migrate\source;

use Drupal\migrate\Plugin\migrate\source\SqlBase;
use Drupal\migrate\Row;

/**
 * Source plugin for ReservoirSource content.
 *
 * @MigrateSource(
 *   id = "uwm_migrate_res_class"
 * )
 */
class UwmMigrateRes extends SqlBase {

  public function fields() {

    $fields = parent::fields();
    $fields += [
      'merged_body' => $this->t('Merged body'),
    ];
    return $fields;

  }

  public function query() {
    // ...
  }

  public function getIds() {
    // ...
  }

  public function prepare($entity, stdClass $row) {
    // Let's pretend we couldn't have done this as easily in prepareRow...
    $entity->title = 'My site: ' . $row->source_title;
  }

  public function prepareRow($row) {

    $field1 = $row->getSourceProperty("field_field1");
    $field2 = $row->getSourceProperty("field_field2");
    $row->setDestinationProperty("merged_body", $field1 . " " . $field2);
    parent::prepareRow($row);

    // Always include this fragment at the beginning of every prepareRow()
    // implementation, so parent classes can ignore rows.
    if (parent::prepareRow($row) === FALSE) {
      // return FALSE;
    }

    $related_data = $this->getRelatedData($row->id);
    // If marked as spam in the related data, skip this row
    if ($related_data->spam) {
      //return FALSE;
    }

    // Add the related data of interest
    $row->foo = $related_data->foo;
    $row->bar = $related_data->bar;
    // return TRUE;
  }

  public function complete($entity, stdClass $row) {
    // Load term.
    $term = taxonomy_term_load($entity->tid);

    // Localized taxonomy term.
    $context = [
      'term',
      $term->tid,
      'description',
    ];
    i18n_string_textgroup('taxonomy')->update_translation($context, 'fr', $row->description_fr);
  }

}
