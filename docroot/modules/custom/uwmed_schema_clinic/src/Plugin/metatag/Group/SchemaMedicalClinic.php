<?php

namespace Drupal\uwmed_schema_clinic\Plugin\metatag\Group;

use Drupal\schema_metatag\Plugin\metatag\Group\SchemaGroupBase;

/**
 * Provides a plugin for the 'Schema.org: Medical Clinic' meta tag group.
 *
 * @MetatagGroup(
 *   id = "schema_medical_clinic",
 *   label = @Translation("Schema.org: Medical Clinic"),
 *   description = @Translation("See Schema.org definitions for this Schema type at <a href="":url"">:url</a>.", arguments = {
 *     ":url" = "https://schema.org/MedicalClinic",
 *   }),
 *   weight = 10,
 * )
 */
class SchemaMedicalClinic extends SchemaGroupBase {
  // Nothing here yet. Just a placeholder class for a plugin.
}
