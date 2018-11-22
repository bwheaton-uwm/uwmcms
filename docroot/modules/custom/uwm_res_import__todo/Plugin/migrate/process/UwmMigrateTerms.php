<?php

namespace Drupal\uwm_res_import\Plugin\migrate\process;


use Drupal\Core\Language\Language;
use Drupal\media\Entity\Media;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use Drupal\paragraphs\Entity\Paragraph;


/**
 * @MigrateProcessPlugin(
 *   id = "uwm_migrate_terms"
 * )
 */
class UwmMigrateTerms extends ProcessPluginBase {


  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {

    $paragraphs = [];

    $fieldName = $value[0]['type'];
    $fieldId = $value[0]['id'];
    if ($fieldName == 'zzzzzzz' && $fieldId) {

      $current = '';
      $data = []; // fetch
      foreach ($data as $dataItem) {

        if (strlen($current)) {
          //$paragraphs[] = static::createTextParagraph($dataItem);

        }

      }

    }


    return $paragraphs;

  }


  /***
   * @param $data
   *
   * @return array
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  static protected function createMedicalServicesTerm($data) {

    $newTerms = [];

    if (!empty($data)) {

      foreach ($data as $key => $timeModel) {


      }


    }

    return $newTerms;

  }


}
