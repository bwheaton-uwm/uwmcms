<?php

namespace Drupal\uwmed_schema_physician\Plugin\metatag\Tag;

use Drupal\schema_metatag\Plugin\metatag\Tag\SchemaNameBase;

/**
 * Provides a plugin for the 'schema_physician_knows_language meta tag.
 *
 * - 'id' should be a globally unique id.
 * - 'name' should match the Schema.org element name.
 * - 'group' should match the id of the group that defines the Schema.org type.
 *
 * @MetatagTag(
 *   id = "schema_physician_knows_language",
 *   label = @Translation("Knows Language"),
 *   description = @Translation("Of a Person, and less typically of an Organization, to indicate a known language."),
 *   name = "knowsLanguage",
 *   group = "schema_physician",
 *   weight = 3,
 *   type = "string",
 *   secure = FALSE,
 *   multiple = TRUE
 * )
 */
class SchemaPhysicianKnowsLanguage extends SchemaNameBase {
  // Nothing here yet. Just a placeholder class for a plugin.
}
