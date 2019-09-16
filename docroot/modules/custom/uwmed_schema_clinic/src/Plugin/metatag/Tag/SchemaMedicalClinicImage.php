<?php

namespace Drupal\uwmed_schema_clinic\Plugin\metatag\Tag;

use Drupal\schema_metatag\Plugin\metatag\Tag\SchemaNameBase;

/**
 * Provides a plugin for the 'schema_physician_image' meta tag.
 *
 * - 'id' should be a globally unique id.
 * - 'name' should match the Schema.org element name.
 * - 'group' should match the id of the group that defines the Schema.org type.
 *
 * @MetatagTag(
 *   id = "schema_clinic_image",
 *   label = @Translation("Clinic Image"),
 *   description = @Translation("REQUIRED BY GOOGLE. The primary image for the clinic."),
 *   name = "image",
 *   group = "schema_medical_clinic",
 *   weight = 2,
 *   type = "image",
 *   secure = FALSE,
 *   multiple = FALSE
 * )
 */
class SchemaMedicalClinicImage extends SchemaNameBase {

  /**
   * {@inheritdoc}
   *
   * Return the stored value, or the default image if the token replacement
   * found empty field value.
   */
  public function value() {

    if (empty($this->value)) {
      // Build URL to the default image, stored in the theme.
      $host = \Drupal::request()->getSchemeAndHttpHost();
      $theme_path = \Drupal::theme()->getActiveTheme()->getPath();
      $default_image = 'src/assets/uwmed_location_card_placeholder.svg';
      return "$host/$theme_path/$default_image";
    }
    else {
      return $this->value;
    }
  }

}
