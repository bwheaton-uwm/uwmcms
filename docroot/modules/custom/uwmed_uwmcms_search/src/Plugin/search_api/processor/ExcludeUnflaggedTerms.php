<?php

namespace Drupal\uwmed_uwmcms_search\Plugin\search_api\processor;

use Drupal\search_api\IndexInterface;
use Drupal\search_api\Processor\ProcessorPluginBase;

/**
 * Prevents indexing of terms that are not flagged.
 *
 * @SearchApiProcessor(
 *   id = "uwm_exclude_unflagged_terms",
 *   label = @Translation("Exclude unflagged terms (UW Medicine)"),
 *   description = @Translation("Prevents terms that are not flagged for inclusion from being indexed for a facet in a search form."),
 *   stages = {
 *     "alter_items" = 0,
 *   },
 * )
 */
class ExcludeUnflaggedTerms extends ProcessorPluginBase {

  /**
   * {@inheritdoc}
   */
  public static function supportsIndex(IndexInterface $index) {
    // Support indexes that contain taxonomy term entities.
    if (in_array('node', $index->getEntityTypes())) {
      return TRUE;
    }

    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function alterIndexedItems(array &$items) {
    // Get the list of terms that should be included.
    $include_terms = [];
    $terms = \Drupal::entityTypeManager()->getStorage('taxonomy_term')->loadByProperties([
      'field_res_include_in_search' => 1,
    ]);
    foreach ($terms as $term) {
      $include_terms[] = $term->label();
    }

    foreach ($items as $item_id => $item) {
      $object = $item->getOriginalObject()->getValue();
      $exclude_item = TRUE;

      // Only alter node entities.
      if (!method_exists($object, 'getEntityType') || $object->getEntityType()->id() != 'node') {
        continue;
      }

      // Define which fields should be checked.
      $fields_to_check = [
        'field_res_expertises_name_string',
      ];

      foreach ($fields_to_check as $field_to_check) {
        $field = $item->getField($field_to_check);
        if (!$field) {
          continue;
        }

        $field_values = $field->getValues();
        if (!$field_values) {
          continue;
        }

        // Remove any values not in the include list.
        foreach ($field_values as $index => $field_value) {
          if (!in_array($field_value, $include_terms)) {
            unset($field_values[$index]);
          }
        }

        $field->setValues($field_values);
        $items[$item_id]->setField($field_to_check, $field);
      }
    }
  }

}
