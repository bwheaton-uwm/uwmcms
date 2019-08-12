<?php

namespace Drupal\uwmed_schema_physician\Plugin\metatag\Tag;

use Drupal\schema_metatag\Plugin\metatag\Tag\SchemaNameBase;

/**
 * Provides a plugin for the 'schema_physician_identifier' meta tag.
 *
 * - 'id' should be a globally unique id.
 * - 'name' should match the Schema.org element name.
 * - 'group' should match the id of the group that defines the Schema.org type.
 *
 * @MetatagTag(
 *   id = "schema_physician_identifier",
 *   label = @Translation("Identifier"),
 *   description = @Translation("The identifier property represents any kind of identifier, such as the NPI."),
 *   name = "identifier",
 *   group = "schema_physician",
 *   weight = 20,
 *   type = "string",
 *   secure = FALSE,
 *   multiple = FALSE
 * )
 */
class SchemaPhysicianIdentifier extends SchemaNameBase {
  // Nothing here yet. Just a placeholder class for a plugin.
}
