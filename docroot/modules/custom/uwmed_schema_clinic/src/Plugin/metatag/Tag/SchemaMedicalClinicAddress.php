<?php

namespace Drupal\uwmed_schema_clinic\Plugin\metatag\Tag;

use Drupal\schema_metatag\Plugin\metatag\Tag\SchemaAddressBase;

/**
 * Provides a plugin for the 'schema_clinic_address' meta tag.
 *
 * Extends `SchemaAddressBase` to produce a 'PostalAddress' type element.
 *
 * - 'id' should be a globally unique id.
 * - 'name' should match the Schema.org element name.
 * - 'group' should match the id of the group that defines the Schema.org type.
 *
 * @MetatagTag(
 *   id = "schema_clinic_address",
 *   label = @Translation("Address"),
 *   description = @Translation("REQUIRED BY GOOGLE? The address of the clinic."),
 *   name = "address",
 *   group = "schema_medical_clinic",
 *   weight = 4,
 *   type = "string",
 *   secure = FALSE,
 *   multiple = FALSE
 * )
 */
class SchemaMedicalClinicAddress extends SchemaAddressBase {

  /**
   * {@inheritdoc}
   *
   * Clean up streetAddress part to remove empty tokens and their commas.
   * Empty tokens may include Building term's address_line2, node's Floor term,
   * and node's Room Number text field.
   */
  public static function outputValue($input_value) {

    if (!empty($input_value['streetAddress'])) {
      $street = trim($input_value['streetAddress']);
      $street = str_replace(" ,", "", $street);
      $street = rtrim($street, ",");
      $input_value['streetAddress'] = $street;
    }

    return $input_value;
  }

}
