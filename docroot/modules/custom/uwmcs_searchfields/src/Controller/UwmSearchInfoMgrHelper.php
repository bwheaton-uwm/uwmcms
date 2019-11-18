<?php

namespace Drupal\uwmcs_searchfields\Controller;

use Drupal\node\NodeInterface;
use Drupal\uwmcs_reader\Controller\UwmFetcher;

/**
 * Controller connects API fields to Drupal fields.
 */
class UwmSearchInfoMgrHelper {

  /**
   * Description here.
   *
   * @param mixed $data
   *   Description here.
   * @param string|null $desiredKeyName
   *   Description here.
   * @param array $currentIndex
   *   Description here.
   *
   * @return array|mixed
   *   Description here.
   */
  public static function extractFirstApiMatch($data, string $desiredKeyName = NULL, array &$currentIndex = []) {

    foreach ((array) $data as $key => $value) {

      if ($key === $desiredKeyName && !empty($value)) {
        return $value;
      }

      elseif (is_array($value) || is_object($value)) {

        self::extractAllApiMatches($value, $desiredKeyName, $currentIndex);

      }

    }

    return (array) $currentIndex;

  }

  /**
   * Description here.
   *
   * @param mixed $data
   *   Description here.
   * @param string|null $desiredKeyName
   *   Description here.
   * @param array $resultArray
   *   Description here.
   *
   * @return array
   *   Description here.
   */
  public static function extractAllApiMatches($data, string $desiredKeyName = NULL, array &$resultArray = []) {

    foreach ((array) $data as $key => $value) {

      if ($key === $desiredKeyName && !empty($value)) {
        $resultArray[] = $value;
      }

      elseif (is_array($value) || is_object($value)) {

        self::extractAllApiMatches($value, $desiredKeyName, $resultArray);

      }

    }

    return (array) $resultArray;

  }

  /**
   * Description here.
   *
   * @param mixed $entity
   *   Description here.
   *
   * @return \stdClass
   *   Description here.
   */
  public static function getEntityApiData($entity) {

    if (!empty($entity) && $entity instanceof NodeInterface) {

      if ($entity->hasField('field_information_manager_url')) {

        if (!empty($entity->field_information_manager_url->value)) {

          $fetcher = new UwmFetcher();
          $apiData = $fetcher->getUrl($entity->field_information_manager_url->value);

          return $apiData;

        }

      }

    }

    return new \stdClass();

  }

}
