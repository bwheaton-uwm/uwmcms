<?php

namespace Drupal\uwmed_schema_physician\Plugin\metatag\Group;

use Drupal\schema_metatag\Plugin\metatag\Group\SchemaGroupBase;

/**
 * Provides a plugin for the 'Physician' meta tag group.
 *
 * @MetatagGroup(
 *   id = "schema_physician",
 *   label = @Translation("Schema.org: Physician"),
 *   description = @Translation("See Schema.org definitions for this Schema type at <a href="":url"">:url</a>. Also see <a href="":url2"">Google's requirements</a>.", arguments = {
 *     ":url" = "https://schema.org/Physician",
 *     ":url2" = "https://developers.google.com/search/docs/data-types/article",
 *   }),
 *   weight = 10,
 * )
 */
class SchemaPhysician extends SchemaGroupBase {
  // Nothing here yet. Just a placeholder class for a plugin.
}
