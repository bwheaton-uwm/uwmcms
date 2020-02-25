<?php

namespace Drupal\uwmcs_reader\Controller;

/**
 * Adds extra data to IM API return values.
 */
class UwmApiClinicFields implements UwmImFieldsInterface {

  /**
   * Description here.
   *
   * @param \stdClass $imApiClinic
   *   Description here.
   */
  public static function addFieldData(\stdClass &$imApiClinic) {

    // If clinic has location, fetch full location details.
    if (!empty($imApiClinic->location->url)) {
      self::getClinicLocationDetails($imApiClinic);
    }

    // If clinic has providers, populate full provider details.
    if (!empty($imApiClinic->careNetworks)) {
      self::getClinicProviderDetails($imApiClinic);
    }

    $imApiClinic->clinicTimeUseMasters = self::getClinicHoursArray($imApiClinic->clinicTimeUseMasters);

  }

  /**
   * Description here.
   *
   * @param array $imApiClinicTimeUseMasters
   *   Description here.
   *
   * @return array
   *   Description here.
   */
  public static function getClinicHoursArray(array $imApiClinicTimeUseMasters = []) {

    // If location has hours, get unix time for javascripts.
    $unixTime = function ($dayOfWeek, $timeOfDay, $useNext = FALSE) {
      $days = [
        'Sunday',
        'Monday',
        'Tuesday',
        'Wednesday',
        'Thursday',
        'Friday',
        'Saturday',
      ];
      $day = $days[$dayOfWeek];
      list($hours, $minutes, $seconds) = explode(':', $timeOfDay);

      // PHP next is the following, not next week:
      $time = strtotime(($useNext ? "Next " : "") . $day);
      $time = strtotime("+ $hours hours", $time);
      $time = strtotime("+ $minutes minutes", $time);
      $time = strtotime("+ $seconds seconds", $time);

      return $time;

    };

    foreach ($imApiClinicTimeUseMasters as &$collection) {

      $opens = $closes = $nextOpens = $nextCloses = [];
      foreach ($collection->hoursOfOperation as &$entry) {

        $opens[] = $unixTime($entry->dayOfWeek, $entry->startTime);
        $closes[] = $unixTime($entry->dayOfWeek, $entry->endTime);
        $nextOpens[] = $unixTime($entry->dayOfWeek, $entry->startTime, TRUE);
        $nextCloses[] = $unixTime($entry->dayOfWeek, $entry->endTime, TRUE);

      }

      $collection->opens = min($opens);
      $collection->closes = min($closes);
      $collection->nextOpens = min($nextOpens);
      $collection->nextCloses = min($nextCloses);

    }

    return $imApiClinicTimeUseMasters;

  }

  /**
   * Description here.
   *
   * @param \stdClass $locationData
   *   Description here.
   */
  public static function getClinicLocationDetails(\stdClass &$locationData) {

    // If clinic has location, fetch full location details.
    if (!empty($locationData->location->url)) {

      $fetcher = new UwmFetcher();

      $location = $fetcher->getUrl($locationData->location->url);
      if (!empty($location->id)) {
        $locationData->location = $location;
      }
    }

  }

  /**
   * Description here.
   *
   * @param \stdClass $locationData
   *   Description here.
   */
  public static function getClinicProviderDetails(\stdClass &$locationData) {

    // If clinic has providers, populate full provider details.
    // @TODO: Change this attach bio-node.
    if (!empty($locationData->careNetworks)) {

      $fetcher = new UwmFetcher();

      foreach ($locationData->careNetworks as &$network) {

        foreach ($network->bioInformations as &$bio) {

          if (!empty($bio->url)) {

            $detail = $fetcher->getUrl($bio->url);
            if (!empty($bio->id)) {
              $bio = $detail;
            }

          }

        }

      }

    }

  }

}
