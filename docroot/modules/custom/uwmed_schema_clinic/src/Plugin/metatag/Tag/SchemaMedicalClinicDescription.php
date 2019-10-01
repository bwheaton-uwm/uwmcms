<?php

namespace Drupal\uwmed_schema_clinic\Plugin\metatag\Tag;

use Drupal\schema_metatag\Plugin\metatag\Tag\SchemaNameBase;
use Drupal\schema_metatag\SchemaMetatagManager;

/**
 * Provides a plugin for the 'schema_clinic_description' meta tag.
 *
 * - 'id' should be a globally unique id.
 * - 'name' should match the Schema.org element name.
 * - 'group' should match the id of the group that defines the Schema.org type.
 *
 * @MetatagTag(
 *   id = "schema_clinic_description",
 *   label = @Translation("Description"),
 *   description = @Translation("A short description of the clinic."),
 *   name = "description",
 *   group = "schema_medical_clinic",
 *   weight = 9,
 *   type = "string",
 *   secure = FALSE,
 *   multiple = FALSE
 * )
 */
class SchemaMedicalClinicDescription extends SchemaNameBase {

  /**
   * {@inheritdoc}
   *
   * Assuming our specific implementation using the node's meta tag field:
   * extract the 'description' meta tag value, as all meta tag values custom to
   * a node are stored together in the field and not separable in the token.
   */
  public function value() {

    $value = $this->value;
    $value_description = $value;

    // Metatag module stores all individual meta tag values on a node as one
    // serialized string. Unserialize (using the Schema.org module's wrapper
    // functions that are more careful) to get at only the 'description'
    // tag value.
    // (If the value is not serialized, it's still the original token - return
    // as-is.)
    if (is_string($value) && SchemaMetatagManager::isSerialized($value)) {
      $value_unserialized = SchemaMetatagManager::unserialize($value);

      if (!empty($value_unserialized['description'])) {
        $value_description = trim($value_unserialized['description']);
      }
      else {
        // If we unserialized and the 'description' tag value is not present,
        // return nothing. By doing so at the point when this method is called,
        // this property will not be included in the output at all. Using the
        // outputValue() method instead to return an empty string would result
        // in including the property with an empty value.
        $value_description = '';
      }

    }

    return $value_description;

  }

}
