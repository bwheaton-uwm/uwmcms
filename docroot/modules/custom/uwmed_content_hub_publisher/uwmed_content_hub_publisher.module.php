<?php

/**
 * @file
 * Publish content hub content when it's imported.
 */

/**
 * Implements hook_acquia_contenthub_drupal_from_cdf_alter().
 */
function uwmed_content_hub_publisher_acquia_contenthub_drupal_from_cdf_alter($entity_type_id, $entity) {
  if ($entity_type_id == 'node') {
    // Obtain the moderation state from the node entity.
    $current_moderation_state = $entity->get('moderation_state')->getValue();

    // If the node is in draft state, we want to publish it.
    if ($current_moderation_state[0]['value'] == 'draft') {

      // Publish the entity.
      $entity->set('moderation_state', 'published');
    }
  }
}
