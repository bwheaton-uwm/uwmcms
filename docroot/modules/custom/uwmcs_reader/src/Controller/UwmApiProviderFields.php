<?php

namespace Drupal\uwmcs_reader\Controller;

/**
 * Adds extra data to IM API return values.
 */
class UwmApiProviderFields implements UwmImFieldsInterface {

  /**
   * Description here.
   *
   * @param \stdClass $imApiProvider
   *   Description here.
   */
  public static function addFieldData(\stdClass &$imApiProvider) {

    // Fetch details for publications.
    // if (!empty($imApiProvider->bioPublications[0]->pubMedId)) {
    // self::getProviderPublications($imApiProvider);
    // }
    // If provider has clinics, get full clinic details.
    if (!empty($imApiProvider->careNetworks[0]->clinicBases)) {
      self::getProviderLocations($imApiProvider);
    }

  }

  /**
   * Description here.
   *
   * @param \stdClass $providerData
   *   Description here.
   */
  public static function getProviderLocations(\stdClass &$providerData) {

    // If provider has clinics, populate full clinic details.
    // @TODO: Change this to node-load locations.
    if (!empty($providerData->careNetworks[0]->clinicBases)) {

      $fetcher = new UwmFetcher();

      foreach ($providerData->careNetworks as &$network) {

        foreach ($network->clinicBases as &$clinic) {

          if (!empty($clinic->url)) {

            $detail = $fetcher->getUrl($clinic->url);
            if (!empty($clinic->id)) {
              $detail->clinicTimeUseMasters =
                UwmApiClinicFields::getClinicHoursArray($detail->clinicTimeUseMasters);

              $clinic = $detail;
            }

          }

        }

      }

    }

  }

  /**
   * Description here.
   *
   * @param \stdClass $providerData
   *   Description here.
   */
  public static function getProviderPublications(\stdClass &$providerData) {

    // Fetch details for publications.
    if (!empty($providerData->bioPublications[0]->pubMedId)) {

      $fetcher = new UwmFetcher();

      $pubIds = [];
      foreach ($providerData->bioPublications as $k => $v) {
        array_push($pubIds, $v->pubMedId);
      }

      $providerData->bioPublicationsDetails = $fetcher->getUrl(
        'https://webservices.uwmedicine.org/api/publications?ids=' .
        implode(',', $pubIds));

    }

  }

}
