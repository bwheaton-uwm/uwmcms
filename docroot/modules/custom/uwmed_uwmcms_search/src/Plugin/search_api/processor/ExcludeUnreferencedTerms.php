<?php

namespace Drupal\uwmed_uwmcms_search\Plugin\search_api\processor;

use Drupal\search_api\IndexInterface;
use Drupal\search_api\Processor\ProcessorPluginBase;

/**
 * Prevents terms that are not referenced by nodes from being indexed.
 *
 * @SearchApiProcessor(
 *   id = "uwm_exclude_unreferenced_terms",
 *   label = @Translation("Exclude unreferenced terms (UW Medicine)"),
 *   description = @Translation("Prevents terms that are not referenced by nodes from being indexed."),
 *   stages = {
 *     "alter_items" = 0,
 *   },
 * )
 */
class ExcludeUnreferencedTerms extends ProcessorPluginBase {

  /**
   * {@inheritdoc}
   */
  public static function supportsIndex(IndexInterface $index) {
    // Support indexes that contain taxonomy term entities.
    if (in_array('taxonomy_term', $index->getEntityTypes())) {
      return TRUE;
    }

    return FALSE;
  }

  /**
   * {@inheritdoc}
   */
  public function alterIndexedItems(array &$items) {
    // Get the node datasource.
    /** @var \Drupal\search_api\Datasource\DatasourceInterface $node_datasource */
    $node_datasource = $this->index->getDatasource('entity:node');
    if (!$node_datasource) {
      return;
    }

    // Get the content types from the datasource.
    $content_types = $node_datasource->getBundles();

    foreach ($items as $item_id => $item) {
      $object = $item->getOriginalObject()->getValue();
      $exclude_item = TRUE;

      // Only alter taxonomy term entities.
      if (!method_exists($object, 'getEntityType') || $object->getEntityType()->id() != 'taxonomy_term') {
        continue;
      }

      $tid = $object->id();

      // Define which fields reference terms.
      $term_fields = [
        'field_res_medical_services',
        'field_res_expertises',
        'field_search_best_bets',
      ];

      foreach ($term_fields as $term_field) {
        $nodes = \Drupal::entityTypeManager()->getStorage('node')->loadByProperties([
          'type' => array_keys($content_types),
          $term_field => $tid,
        ]);

        if (!empty($nodes)) {
          $exclude_item = FALSE;
        }
      }

      // If no nodes reference this term, exclude it from the index.
      if ($exclude_item) {
        unset($items[$item_id]);
      }
    }
  }

}
