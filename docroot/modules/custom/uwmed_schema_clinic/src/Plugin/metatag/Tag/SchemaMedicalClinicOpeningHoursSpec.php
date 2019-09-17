<?php

namespace Drupal\uwmed_schema_clinic\Plugin\metatag\Tag;

use Drupal\schema_metatag\Plugin\metatag\Tag\SchemaNameBase;

/**
 * Provides a plugin for the 'schema_clinic_opening_hours_spec' meta tag.
 *
 * - 'id' should be a globally unique id.
 * - 'name' should match the Schema.org element name.
 * - 'group' should match the id of the group that defines the Schema.org type.
 *
 * Note: 'multiple' is set TRUE only to specially handle urgent care hours in
 * addition to main/primary care hours. (See inline comments.)
 *
 * @MetatagTag(
 *   id = "schema_clinic_opening_hours_spec",
 *   label = @Translation("Opening Hours Specification"),
 *   description = @Translation("The daily open hours of the clinic, described by OpeningHoursSpecification objects."),
 *   name = "openingHoursSpecification",
 *   group = "schema_medical_clinic",
 *   weight = 7,
 *   type = "string",
 *   secure = FALSE,
 *   multiple = TRUE
 * )
 */
class SchemaMedicalClinicOpeningHoursSpec extends SchemaNameBase {

  /**
   * {@inheritdoc}
   *
   * Parse hours data from node field's JSON value and format into
   * `OpeningHoursSpecification` objects.
   */
  public function output() {

    $output = '';

    $value = $this->value();

    if (!empty($value) && is_string($value)) {

      // We expect the token to pull `field_uwm_json_packet`, a JSON string
      // which includes the hours data from Reservoir paragraphs values.
      // Try to decode JSON string.
      $value = json_decode($value, TRUE);

      if (!empty($value) && is_array($value) && !empty($value['hours_of_operation'])) {

        // Build the structure according to `OpeningHoursSpecification` type.
        // @see https://schema.org/OpeningHoursSpecification
        // @see https://developers.google.com/search/docs/data-types/local-business#business_hours
        $meta = [];

        $day_map = [
          0 => "Sunday",
          1 => "Monday",
          2 => "Tuesday",
          3 => "Wednesday",
          4 => "Thursday",
          5 => "Friday",
          6 => "Saturday",
        ];

        // Each value is from an Hours paragraph on Reservoir.
        foreach ($value['hours_of_operation'] as $hours) {

          // Use a friendly type name as the key.
          $type = $hours['hours_type'];
          if ($type == 0) {
            // This may be primary care or general hours.
            $type = 'operation';
          }
          elseif ($type == 1) {
            $type = 'urgent';
          }

          // Initialize an `OpeningHoursSpecification` object for each day of
          // the week, with closed hours (indicated as 00:00 per Google spec).
          // The loop below fills in hours by pulling from paragraph data -
          // if closed on a day, there is no corresponding paragraph for it, so
          // this ensures closed days are represented.
          // Incoming days are keyed as 0-6 = Sunday-Saturday.
          for ($i = 0; $i < 7; $i++) {

            $meta[$type]['days'][$i] = [
              '@type' => 'OpeningHoursSpecification',
              'dayOfWeek' => $day_map[$i],
              'opens' => '00:00',
              'closes' => '00:00',
            ];

          }

          // Track boolean for always open by initializing as true, and setting
          // false if any individual day has non-all-day hours.
          $meta[$type]['always_open'] = TRUE;

          // If no hours data for individual days, set always open to FALSE.
          if (empty($hours['hours'])) {
            $meta[$type]['always_open'] = FALSE;
          }

          // Process each day's hours.
          if (!empty($hours['hours'])) {
            foreach ($hours['hours'] as $day) {

              // Skip any partial/empty data.
              if (!is_numeric($day['day'])) {
                continue;
              }

              $day_i = (int) $day['day'];

              // If both open and close times are present, we have proper
              // values for a day's hours (otherwise it remains default of
              // closed).
              if (!empty($day['start']) && !empty($day['end'])) {

                // If hours for any day are not 12:00am-11:59pm, then it's not
                // always open.
                if ($day['start'] !== '00:00:00' || $day['end'] !== '23:59:00') {
                  $meta[$type]['always_open'] = FALSE;
                }

                // Format open and close times, per spec.
                // Incoming: 24-hour with seconds, e.g. "15:30:00".
                // Desired: 24-hour without seconds, e.g. "15:30".
                $meta[$type]['days'][$day_i]['opens'] = substr($day['start'], 0, strrpos($day['start'], ':'));
                $meta[$type]['days'][$day_i]['closes'] = substr($day['end'], 0, strrpos($day['end'], ':'));

              }

            }
          }

        }

        // Pass hours of operation (either the only set of hours, or the
        // primary care hours) as the main value.
        // Note: the `OpeningHoursSpecification` object does not have a special
        // format to indicate "always open". So, we don't need to output that
        // separately or differently from the per-day hour values.
        if (!empty($meta['operation'])) {

          // We've set the plugin to allow "multiple" values, to enable us to
          // return more than one top-level schema "tag". Thus the return value
          // is formatted as an indexed array.
          // @see \Drupal\metatag\MetatagManager::generateRawElements()
          $output = [
            [
              '#tag' => 'meta',
              '#attributes' => [
                'name' => $this->name,
                'content' => static::outputValue($meta['operation']['days']),
                'group' => $this->group,
                'schema_metatag' => TRUE,
              ],
            ],
          ];

        }

        if (!empty($meta['urgent'])) {

          if (!is_array($output)) {

            // Not sure if there are any cases of urgent hours present without
            // main/primary hours, but handle it in case: make urgent the main
            // hours.
            $output = [
              [
                '#tag' => 'meta',
                '#attributes' => [
                  'name' => $this->name,
                  'content' => static::outputValue($meta['urgent']['days']),
                  'group' => $this->group,
                  'schema_metatag' => TRUE,
                ],
              ],
            ];

          }
          else {

            // Add urgent care hours, as a 'department'.
            // @see https://developers.google.com/search/docs/data-types/local-business#multiple_departments
            $output[] = [
              '#tag' => 'meta',
              '#attributes' => [
                'name' => 'department',
                'content' => [
                  // This nests a 'MedicalClinic' as a department within our
                  // top-level 'MedicalClinic'. It feels weird, but is
                  // nonetheless the most accurate representation.
                  '@type' => 'MedicalClinic',
                  'name' => 'Urgent Care',
                  'openingHoursSpecification' => static::outputValue($meta['urgent']['days']),
                ],
                'group' => $this->group,
                'schema_metatag' => TRUE,
              ],
            ];

          }

        }

      }

    }

    return $output;

  }

}
