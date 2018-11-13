<?php

namespace Drupal\uwmcs_extension;

use Drupal\uwmcs_reader\Controller\UwmMapper;

/**
 * A Twig extension that adds custom functions/filters.
 */
class TwigExtension extends \Twig_Extension {

  /**
   * Generates a list of all Twig functions that this extension defines.
   *
   * @return array
   *   A key/value array that defines custom Twig functions. The key denotes the
   *   function name used in the tag, e.g.:
   *
   * @code
   *   {{ uwm_test_func() }}
   * @endcode
   *
   *   The value is a standard PHP callback that defines what the function does.
   */
  public function getFunctions() {
    return [
      new \Twig_SimpleFunction(
        'uwm_test_func', [$this, 'testFunction']),
      new \Twig_SimpleFunction(
        'uwm_get_path_nid', [$this, 'getPathNid']),
      new \Twig_SimpleFunction(
        'uwm_get_api_nid', [$this, 'getApiPathNid']),
      new \Twig_SimpleFunction(
        'uwm_extract_parts', [$this, 'extractArrayValues']),
      new \Twig_SimpleFunction(
        'uwm_get_sharepoint_location_image', [$this, 'getSharepointLocationImage']),
    ];
  }

  /**
   * Generates a list of all Twig filters that this extension defines.
   *
   * @return array
   *   A key/value array that defines custom Twig filters. The key denotes the
   *   filter name used in the tag, e.g.:
   *
   * @code
   *   {{ foo|uwm_test_filter }}
   * @endcode
   *
   *   The value is a standard PHP callback that defines what the filter does.
   */
  public function getFilters() {
    return [
      new \Twig_SimpleFilter(
        'uwm_test_filter', [$this, 'testFilter']),
      new \Twig_SimpleFilter(
        'uwm_replace_markup', [$this, 'convertInlineStyles']),
      new \Twig_SimpleFilter(
        'uwm_join_parts', [$this, 'joinArray']),
      new \Twig_SimpleFilter(
        'uwm_sort_parts', [$this, 'sortArrayByValues']),
      new \Twig_SimpleFilter(
        'uwm_format_phone', [$this, 'formatPhone']),
      new \Twig_SimpleFilter(
        'uwm_arraycount_styles', [$this, 'collectionCssClasses']),
      new \Twig_SimpleFilter(
        'uwm_format_accent_class', [$this, 'formatAccentClass']),

    ];
  }

  /**
   * Gets a unique identifier for this Twig extension.
   *
   * @return string
   *   A unique identifier for this Twig extension.
   */
  public function getName() {
    return 'uwmcs_extension';
  }

  /**
   * Description text.
   *
   * @param bool $upperCase
   *   Description text.
   *
   * @return string
   *   Description text.
   */
  public static function testFunction($upperCase = FALSE) {
    $string = "The quick brown box jumps over the lazy dog 123.";
    if ($upperCase) {
      return strtoupper($string);
    }
    else {
      return strtolower($string);
    }
  }

  /**
   * Description text.
   *
   * @param string $string
   *   Description text.
   *
   * @return string
   *   Description text.
   */
  public static function testFilter(string $string) {
    return strtolower($string);
  }

  /**
   * Description text.
   *
   * @param string $string
   *   Description text.
   *
   * @return string
   *   Description text.
   */
  public static function getPathNid(string $string = NULL) {

    if ($string) {
      return UwmMapper::getNidByPathAlias($string);
    }

  }

  /**
   * Description text.
   *
   * @param string $string
   *   Description text.
   *
   * @return string
   *   Description text.
   */
  public static function getApiPathNid(string $string = NULL) {

    if ($string) {
      return UwmMapper::getNidByInformationManagerUri($string);
    }

  }

  /**
   * Description text.
   *
   * @param string $string
   *   Description text.
   *
   * @return string
   *   Description text.
   */
  public static function convertInlineStyles(string $string = '') {

    $patterns = [
      '/(style="[^"]?italic[^>]+>)([^<]+)/',
      '/(style="[^"]?bold[^>]+>)([^<]+)/',
      '/<style .*style>/s',
    ];

    $replacements = [
      '$1<em>$2</em>',
      '$1<strong>$2</strong>',
      '',
    ];

    return preg_replace($patterns, $replacements, $string);

  }

  /**
   * Description text.
   *
   * @param array $parts
   *   Description text.
   * @param string $separator
   *   Description text.
   *
   * @return string
   *   Description text.
   */
  public static function joinArray(array $parts = NULL, string $separator = ', ') {

    $cleanArr = [];

    foreach ((array) $parts as $part) {
      if (is_array($part)) {
        $part = self::joinArray($part, $separator);
      }
      $cleanPart = trim(
        preg_replace('/\s+/', ' ', $part)
      );
      if (!empty($cleanPart)) {
        $cleanArr[] = $cleanPart;
      }
    }

    return implode($separator, $cleanArr);

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
   * @code
   * These are all valid:
   * {{ uwm_extract_parts(clinic, 'expertiseName') | uwm_join_parts(',<br>') |
   *   raw }}
   * {{ uwm_extract_parts(clinic.expertise, 'expertiseName') | slice(0, 4) |
   *   uwm_join_parts(',<br>') | raw }}
   * @endcode
   *
   * @return array
   *   Description here.
   */
  public static function extractArrayValues($data = [], string $desiredKeyName = NULL, array &$resultArray = []) {

    foreach ((array) $data as $key => $value) {

      if ($key === $desiredKeyName) {
        if (is_array($value)) {
          $resultArray = $resultArray + $value;
        }
        else {
          $resultArray[] = $value;
        }
      }

      elseif (is_array($value) || is_object($value)) {

        self::extractArrayValues($value, $desiredKeyName, $resultArray);

      }

    }

    return (array) $resultArray;

  }

  /**
   * Try to get a clinic image URL SharePoint or return generic placeholder.
   *
   * @param string $clinicUrl
   *   A clinicUrl formatted like /locations/clinic-name.
   *
   * @return string
   *   Returns a URL for either the clinic image or a generic placeholder.
   */
  public static function getSharepointLocationImage(string $clinicUrl = NULL) {

    $imageUrl = NULL;
    if ($clinicUrl) {
      $url = 'https://www.uwmedicine.org' . $clinicUrl . '/PublishingImages/splash1.jpg';
      $responseCode = get_headers($url);
      $imageExists = $responseCode[0] == ('HTTP/2 200' or 'HTTP/1.1 200 OK');
      if ($imageExists) {
        $imageUrl = $url;
      }
    }
    else {
      $imageUrl = "/themes/custom/uwmed/dist/assets/missing-img-horizontal.png";
    }
    return $imageUrl;
  }

  /**
   * Description here.
   *
   * @param mixed $data
   *   Description here.
   * @param string|null $sortKey
   *   Description here.
   *
   * @return mixed
   *   Description here.
   */
  public static function sortArrayByValues($data = [], string $sortKey = NULL) {

    if (!is_array($data)) {
      return $data;
    }

    usort($data, function ($a, $b) use ($sortKey) {

      if (isset($sortKey)) {

        if (is_array($a) && isset($a[$sortKey])) {

          return $a[$sortKey] <=> $b[$sortKey];

        }
        elseif (is_object($a) && isset($a->{$sortKey})) {

          return $a->{$sortKey} <=> $b->{$sortKey};

        }
      }

      return $a <=> $b;

    });

    return $data;

  }

  /**
   * Description text.
   *
   * @param string $phone
   *   Description text.
   * @param string $separator
   *   Description text.
   *
   * @return null|string
   *   Description text.
   */
  public static function formatPhone(string $phone = '', string $separator = '-') {

    $digits = preg_replace('/[^0-9]/', '', $phone);

    if (strlen($digits) > 10) {
      return implode(
        [
          substr($digits, 0, strlen($digits) - 10),
          '(' . substr($digits, -10, 3) . ')',
          substr($digits, -7, 3),
          substr($digits, -4, 4),
        ], $separator);

    }
    elseif (strlen($digits) == 10) {
      return implode(
        [
          substr($digits, 0, 3),
          substr($digits, 3, 3),
          substr($digits, 6, 4),
        ], $separator);

    }
    elseif (strlen($digits) == 7) {
      return implode(
        [
          substr($digits, 0, 3),
          substr($digits, 3, 4),
        ], $separator);
    }

    return NULL;
  }

  /**
   * Description text.
   *
   * @param mixed|null $collectionItems
   *   Description text.
   *
   * @return array
   *   Description text.
   */
  public static function collectionCssClasses($collectionItems = NULL) {

    $cssClasses = [];

    if (method_exists($collectionItems, 'getValue')) {
      $collectionItems = $collectionItems->getValue();
    }

    $collection = (array) $collectionItems;
    $cssClasses[] = 'group-of-' . count($collection) . '-total';
    for ($i = 2; $i <= 10; $i++) {
      if (count($collection) % $i === 0) {
        $cssClasses[] = 'group-of-' . $i . 's';
      }
    }

    return $cssClasses;

  }

  /**
   * Description text.
   *
   * @param string|null $accentColor
   *   Description text.
   *
   * @return string
   *   Description text.
   */
  public static function formatAccentClass(string $accentColor = '') {

    $suffixPattern = '/(.*)\s\(.*\)/';
    $suffixReplacement = '${1}';
    $accentClass = preg_replace($suffixPattern, $suffixReplacement, $accentColor);

    $spacePattern = '/\s/';
    $spaceReplacement = '-';
    $accentClass = preg_replace($spacePattern, $spaceReplacement, $accentClass);
    return strtolower($accentClass);

  }

}
