<?php

namespace Drupal\uwmcs_reader\Controller;

/**
 * Adds extra data to IM API return values.
 */
interface UwmImFieldsInterface {

  /**
   * Description here.
   *
   * @param \stdClass $imApiItem
   *   Description here.
   *
   * @return mixed
   *   Description here.
   */
  public static function addFieldData(\stdClass &$imApiItem);

}
