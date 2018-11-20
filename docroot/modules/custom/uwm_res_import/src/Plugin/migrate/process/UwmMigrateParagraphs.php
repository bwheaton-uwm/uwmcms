<?php

namespace Drupal\uwm_res_import\Plugin\migrate\process;


use Drupal\Core\Language\Language;
use Drupal\media\Entity\Media;
use Drupal\migrate\MigrateExecutableInterface;
use Drupal\migrate\ProcessPluginBase;
use Drupal\migrate\Row;
use Drupal\paragraphs\Entity\Paragraph;


/**
 * Perform custom value transformations.
 *
 * @MigrateProcessPlugin(
 *   id = "uwm_migrate_paragraphs"
 * )
 *
 * To do custom value transformations use the following:
 *
 * @code
 * field_text:
 *   plugin: transform_value
 *   source: text
 * @endcode
 *
 */
class UwmMigrateParagraphs extends ProcessPluginBase {


  /**
   * {@inheritdoc}
   */
  public function transform($value, MigrateExecutableInterface $migrate_executable, Row $row, $destination_property) {

    $paragraphs = [];

    $fieldName = $value[0]['type'];
    $fieldId = $value[0]['id'];
    if($fieldName == 'paragraph--hours_of_operation' && $fieldId) {

      $current = '';
      $data = []; // fetch
      foreach ($data as $dataItem) {

        if (strlen($current)) {
          $paragraphs[] = static::createTextParagraph($dataItem);

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
  static protected function createDaysParagraph($data) {

    $newParagraphs = [];

    if (!empty($data)) {

      foreach ($data as $key => $timeModel) {

        $paragraph = Paragraph::create(['type' => 'hours_of_operation']);

        //$models = [0 => 'Hours of Operation', 1 => 'Urgent Care Hours'];
        $paragraph->set('field_res_hours_type', ($timeModel->timeModelId - 1));
        $paragraph->set('field_res_is_always_open', (bool) $timeModel->hoursOfOperation);

        if ($paragraph->save()) {

          $subItems = [];
          foreach ($timeModel->hoursOfOperation as $day) {
            $subItems[] = self::createHoursParagraph($day, $paragraph);
          }
          $paragraph->set('field_res_hours', $subItems);

        }

        if ($paragraph->save()) {

          $newParagraphs[] = [
            'target_id' => $paragraph->id(),
            'target_revision_id' => $paragraph->getRevisionId(),
          ];

        }

      }

    }

    return $newParagraphs;

  }


  /***
   * @param $dayHours
   * @param $parentParagraph
   *
   * @return array
   * @throws \Drupal\Core\Entity\EntityStorageException
   */
  static protected function createHoursParagraph($dayHours, $parentParagraph) {

    $paragraph = Paragraph::create(['type' => 'hours_for_day']);

    $paragraph->set('parent_type', 'paragraph');
    $paragraph->set('parent_field_name', 'field_hours');
    $paragraph->set('parent_id', $parentParagraph->id());
    $paragraph->set('field_res_day', $dayHours->dayOfWeek);
    $paragraph->set('field_res_start_time', $dayHours->startTime);
    $paragraph->set('field_res_end_time', $dayHours->endTime);
    //$paragraph->isNew();

    if ($paragraph->save()) {

      return ([
        'target_id' => $paragraph->id(),
        'target_revision_id' => $paragraph->getRevisionId(),
      ]);

    }

  }


  /**
   * Create a Text Area paragraph.
   *
   * @param string $blob
   *   The HTML string to use as the main text field.
   *
   * @return int[]
   *   An array of entity/revision IDs keyed by 'target_id' and
   *   'target_revision_id'.
   */
  static protected function createTextParagraph($blob) {
    $paragraph = Paragraph::create([
      'type' => 'text_area',
      'field_text_area' => [
        'value' => $blob,
        'format' => 'basic_html',
      ],
    ]);
    $paragraph->save();

    return [
      'target_id' => $paragraph->id(),
      'target_revision_id' => $paragraph->getRevisionId(),
    ];
  }

  /**
   * Create a Media paragraph.
   *
   * Save the file locally, creating a File entity of type image.
   * Create a Media entity of type image referencing that File entity.
   * Create a Media paragraph referencing that Media entity.
   *
   * @param string $blob
   *   The HTML string to use as the main text field. It should contain an <img>
   *   element. If there is more than one, only the first is used.
   *
   * @return int[]
   *   An array of entity/revision IDs keyed by 'target_id' and
   *   'target_revision_id'.
   */
  static protected function createMediaParagraph($blob) {


    // Create the Media entity.
    $media = Media::create([
      'bundle' => 'image',
      'uid' => '9',
      // 'langcode' => Language::LANGCODE_DEFAULT,
      // 'status' => Media::PUBLISHED,
      'field_media_image' => [
        'target_id' => $file->id(),
        'alt' => $alt_text,
      ],
    ]);
    $media->save();

    // Create the Media paragraph entity.
    $paragraph = Paragraph::create([
      'type' => 'media',
      'field_media' => $media->id(),
    ]);
    $paragraph->save();

    return [
      'target_id' => $paragraph->id(),
      'target_revision_id' => $paragraph->getRevisionId(),
    ];
  }

}
