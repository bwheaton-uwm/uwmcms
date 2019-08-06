<?php

namespace Drupal\uwmed_schema_physician\Plugin\metatag\Tag;

use Drupal\schema_metatag\Plugin\metatag\Tag\SchemaNameBase;

/**
 * Provides a plugin for the 'schema_physician_name' meta tag.
 *
 * - 'id' should be a globally unique id.
 * - 'name' should match the Schema.org element name.
 * - 'group' should match the id of the group that defines the Schema.org type.
 *
 * @MetatagTag(
 *   id = "schema_physician_name",
 *   label = @Translation("Name"),
 *   description = @Translation("REQUIRED BY GOOGLE. The name of the physician."),
 *   name = "name",
 *   group = "schema_physician",
 *   weight = -9,
 *   type = "string",
 *   secure = FALSE,
 *   multiple = FALSE
 * )
 */
class SchemaPhysicianName extends SchemaNameBase {
  // Nothing here yet. Just a placeholder class for a plugin.
}
