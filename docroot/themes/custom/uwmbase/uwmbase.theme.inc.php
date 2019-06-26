<?php

/**
 * @file
 * Helper file with extra routines or processing for uwmbase.theme.
 */

/**
 * If a node has field_uwm_json_packet, parse its JSON data into arrays.
 *
 * Process some specific data and save result in $node->uwm_json_packet_data.
 *
 * @param array|null $json_data
 *   An array of data from json field.
 *
 * @return array|mixed
 *   Array of parsed clinic hours.
 */
function _uwmbase_get_clinic_hours_array(array $json_data = NULL) {

  if (!empty($json_data) && is_array($json_data)) {

    // Process operation hours data.
    if (!empty($json_data['hours_of_operation'])) {

      // Restructure the data.
      $data = [];

      // Each value is from an Hours paragraph on Reservoir.
      foreach ($json_data['hours_of_operation'] as $hours) {

        // Use a friendly type name as the key.
        $type = $hours['hours_type'];
        if ($type == 0) {
          // This may be primary care or general hours - logic in template.
          $type = 'operation';
        }
        elseif ($type == 1) {
          $type = 'urgent';
        }

        // Incoming days are keyed as 0-7 = Sunday-Saturday.
        // If closed on a day, there is no corresponding paragraph for it.
        // This initial array ensures each day is present; if a paragraph does
        // not fill in the times for a day, we know it's a closed day.
        $data[$type]['days'] = [
          0 => [
            'day_name' => t("Sunday"),
          ],
          1 => [
            'day_name' => t("Monday"),
          ],
          2 => [
            'day_name' => t("Tuesday"),
          ],
          3 => [
            'day_name' => t("Wednesday"),
          ],
          4 => [
            'day_name' => t("Thursday"),
          ],
          5 => [
            'day_name' => t("Friday"),
          ],
          6 => [
            'day_name' => t("Saturday"),
          ],
        ];

        // Determine boolean for always open.
        $data[$type]['always_open'] = TRUE;

        // Note: while the Reservoir hours paragraph has a boolean field for
        // "Is always open?", it does not appear to be used. Thus, only use
        // its value if it's actually true (otherwise, empty would also
        // convert to false). Check also for every day of the week specified
        // as 12:00am-11:59pm.
        if (isset($hours['always_open'])) {
          if ((boolean) $hours['always_open']) {
            $data[$type]['always_open'] = TRUE;
          }
        }

        // Set empty defaults for the timestamps for open/close today and
        // next open day.
        $data[$type]['open_close'] = [
          'opens_today' => NULL,
          'closes_today' => NULL,
          'opens_next' => NULL,
          'closes_next' => NULL,
        ];

        // Process each day's hours.
        if (!empty($hours['hours'])) {
          foreach ($hours['hours'] as $day) {

            // If no hours, it's closed on this day.
            if (empty($day['start']) || empty($day['end'])) {
              continue;
            }

            $day_i = (int) $day['day'];

            // If hours for any day are not 12:00am-11:59pm, then this is not
            // always open.
            if ($day['start'] !== '00:00:00' || $day['end'] !== '23:59:00') {
              $data[$type]['always_open'] = FALSE;
            }

            // Save the times in original format for use in open/closed loop
            // afterward.
            $data[$type]['days'][$day_i]['start_hms'] = $day['start'];
            $data[$type]['days'][$day_i]['end_hms'] = $day['end'];

            // Format times.
            // Incoming: 24-hour with seconds, e.g. "15:30:00".
            // Display: 12-hour with a/p, e.g. "3:30p".
            $times = [
              $day['start'],
              $day['end'],
            ];

            foreach ($times as &$time) {

              list($h, $m, $s) = explode(':', $time);

              // Determine am/pm.
              $h = (int) $h;

              $a = ($h < 12 ? 'am' : 'pm');

              // Convert 24-hour to 12-hour display.
              if ($h === 0) {
                $h = 12;
              }
              elseif ($h > 12) {
                $h = $h - 12;
              }

              $time = ($h . ':' . $m . ' ' . $a);
            }

            // Add this day's times.
            $data[$type]['days'][$day_i]['start'] = $times[0];
            $data[$type]['days'][$day_i]['end'] = $times[1];

          }

          // Get the open/close timestamps for today and the next open day,
          // unless always open.
          if (!$data[$type]['always_open']) {

            // All locations are in the Pacific time zone.
            $tz = new \DateTimeZone('America/Los_Angeles');

            $today = new \DateTime('today', $tz);
            $today_ymd = $today->format('Y-m-d');

            // Get today as numeric day of the week.
            $today_i = (int) $today->format('w');

            if (!empty($data[$type]['days'][$today_i]['start_hms'])) {
              $date_opens = new \DateTime($today_ymd . ' ' . $data[$type]['days'][$today_i]['start_hms'], $tz);
              $date_closes = new \DateTime($today_ymd . ' ' . $data[$type]['days'][$today_i]['end_hms'], $tz);

              $data[$type]['open_close']['opens_today'] = $date_opens->format('U');
              $data[$type]['open_close']['closes_today'] = $date_closes->format('U');
            }

            // Now find the next day of the week that has hours.
            // Track how many days we move forward from today, to find the
            // actual date of that next open day.
            $next_i = $today_i;
            for ($next_add = 1; $next_add < 7; $next_add++) {
              $next_i = ($next_i + 1) % 7;

              if (!empty($data[$type]['days'][$next_i]['start_hms'])) {
                $date_opens = new \DateTime('+' . $next_add . 'days ' . $data[$type]['days'][$next_i]['start_hms'], $tz);
                $date_closes = new \DateTime('+' . $next_add . 'days ' . $data[$type]['days'][$next_i]['end_hms'], $tz);

                $data[$type]['open_close']['opens_next'] = $date_opens->format('U');
                $data[$type]['open_close']['closes_next'] = $date_closes->format('U');

                break;
              }
            }

          }

          // Finally, move Sunday from 0 to 7 to match our display order.
          // Then the template can loop and print each day's hours directly.
          $data[$type]['days'][7] = $data[$type]['days'][0];
          unset($data[$type]['days'][0]);

        }

      }

      $json_data['hours_of_operation'] = $data;

    }

    // Save data to node.
    return $json_data['hours_of_operation'];

  }

}
