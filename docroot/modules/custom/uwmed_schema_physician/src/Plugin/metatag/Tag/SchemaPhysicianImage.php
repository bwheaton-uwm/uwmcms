<?php

namespace Drupal\uwmed_schema_physician\Plugin\metatag\Tag;

use Drupal\schema_metatag\Plugin\metatag\Tag\SchemaNameBase;

/**
 * Provides a plugin for the 'schema_physician_image' meta tag.
 *
 * - 'id' should be a globally unique id.
 * - 'name' should match the Schema.org element name.
 * - 'group' should match the id of the group that defines the Schema.org type.
 *
 * @MetatagTag(
 *   id = "schema_physician_image",
 *   label = @Translation("Physician Image"),
 *   description = @Translation("The primary image for the physician."),
 *   name = "image",
 *   group = "schema_physician",
 *   weight = 4,
 *   type = "image",
 *   secure = FALSE,
 *   multiple = FALSE
 * )
 */
class SchemaPhysicianImage extends SchemaNameBase {

  /**
   * {@inheritdoc}
   *
   * Removes the query string from the URL.
   */
  public static function outputValue($input_value) {
    // Nothing here yet. Just a placeholder class for a plugin.
  }

}
