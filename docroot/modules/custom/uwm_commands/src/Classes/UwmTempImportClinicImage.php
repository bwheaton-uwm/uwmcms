<?php

namespace Drupal\uwm_commands\Commands;

use Drupal\node\Entity\Node;

/**
 * Quick routines for one-time import of clinic images.
 *
 * This script is designed to copy images from the @uwmed Drupal site to
 * the @stevie Drupal site, using the clinic's image field. It relies
 * on an input file that has a mapping of the source images along with
 * destination node-id.  For example:
 *
 * @code
 *
 *  [{
 * "fid": 601,
 * "image_filename": "UW-Neighborhood-Shoreline-Clinic-Urgent-Care-New.jpg",
 * "stevie_nid": 22221,
 * "image_uri": "public://UW-Neighborhood-Shoreline-Clinic-Urgent-Care-New.jpg"
 * },]
 *
 * @endcode
 */
class UwmTempImportClinicImage {

  /**
   * Description here.
   */
  public function saveClinicImagesLocally(string $mappingFile = NULL) {

    $data = $this->readJson($mappingFile);

    foreach ($data as $item) {

      $node = Node::load($item['stevie_nid']);
      $newImage = self::saveImage($item['image_uri']);

      $this->saveNode($newImage, $node);

    }

  }

  /**
   * Description here.
   */
  private function saveNode($file, $node) {

    if ($file && $node) {

      $image = [
        'target_id' => $file->id(),
        'alt' => $node->title->value,
        'title' => $node->title->value,
      ];

      $node->set('field_res_image', $image);
      return $node->save();

    }

  }

  /**
   * Description here.
   */
  private function saveImage(string $imagePath = NULL) {

    $drupalPath = $imagePath;
    $sourceUrl = 'https://cms.uwmedicine.org/sites/default/files/' .
                 str_ireplace('public://', '', $drupalPath);

    $existingFile = \Drupal::entityTypeManager()->getStorage('file')
      ->loadByProperties(['uri' => $drupalPath]);

    if (is_array($existingFile)) {
      $file = array_shift($existingFile);
    }

    if ($file->id()) {
      return $file;
    }

    $fileData = file_get_contents($sourceUrl);
    if ($fileData) {
      $file = file_save_data($fileData, $drupalPath, FILE_EXISTS_RENAME);
    }

    return $file;
  }

  /**
   * Description here.
   */
  private function readJson(string $filePath = NULL) {

    $string = file_get_contents($filePath);
    $data = json_decode($string, TRUE);

    return $data;
  }

}
