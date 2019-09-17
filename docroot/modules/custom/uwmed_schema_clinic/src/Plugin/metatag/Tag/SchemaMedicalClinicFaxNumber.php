<?php

namespace Drupal\uwmed_schema_clinic\Plugin\metatag\Tag;

use Drupal\schema_metatag\Plugin\metatag\Tag\SchemaNameBase;

/**
 * Provides a plugin for the 'schema_clinic_fax_number' meta tag.
 *
 * - 'id' should be a globally unique id.
 * - 'name' should match the Schema.org element name.
 * - 'group' should match the id of the group that defines the Schema.org type.
 *
 * @MetatagTag(
 *   id = "schema_clinic_fax_number",
 *   label = @Translation("Fax number"),
 *   description = @Translation("The fax number of the clinic."),
 *   name = "faxNumber",
 *   group = "schema_medical_clinic",
 *   weight = 6,
 *   type = "string",
 *   secure = FALSE,
 *   multiple = FALSE
 * )
 */
class SchemaMedicalClinicFaxNumber extends SchemaNameBase {
  // Nothing here yet. Just a placeholder class for a plugin.
}
