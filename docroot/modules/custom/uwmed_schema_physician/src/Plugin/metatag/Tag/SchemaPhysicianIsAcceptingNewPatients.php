<?php

namespace Drupal\uwmed_schema_physician\Plugin\metatag\Tag;

use Drupal\schema_metatag\Plugin\metatag\Tag\SchemaNameBase;

/**
 * A plugin for the 'schema_physician_is_accepting_new_patients' meta tag.
 *
 * - 'id' should be a globally unique id.
 * - 'name' should match the Schema.org element name.
 * - 'group' should match the id of the group that defines the Schema.org type.
 *
 * @MetatagTag(
 *   id = "schema_physician_is_accepting_new_patients",
 *   label = @Translation("Is Accepting New Patients"),
 *   description = @Translation("Whether the provider is accepting new patients."),
 *   name = "isAcceptingNewPatients",
 *   group = "schema_physician",
 *   weight = 2,
 *   type = "string",
 *   secure = FALSE,
 *   multiple = FALSE
 * )
 */
class SchemaPhysicianIsAcceptingNewPatients extends SchemaNameBase {

  /**
   * {@inheritdoc}
   *
   * Converts the value to "true" or "false".
   */
  public static function outputValue($input_value) {
    return ($input_value == '1') ? 'true' : 'false';
  }

}
