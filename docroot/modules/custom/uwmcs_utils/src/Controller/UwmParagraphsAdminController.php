<?php

namespace Drupal\uwmcs_utils\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\paragraphs\ParagraphInterface;

/**
 * Controller for a custom admin page for listing paragraphs and parents.
 */
class UwmParagraphsAdminController extends ControllerBase {

  /**
   * Page callback for /admin/uwm/paragraphs.
   *
   * @param string $paragraph_type
   *   A paragraph bundle machine name.
   *
   * @return array
   *   The page content render array.
   */
  public function paragraphsListing($paragraph_type = NULL) {

    $build = [];

    if (empty($paragraph_type)) {

      $path = '/admin/uwm/paragraphs';

      $types = \Drupal::entityTypeManager()->getStorage('paragraphs_type')->loadMultiple();

      foreach ($types as $type) {
        $build[] = '<a href="' . $path . '/' . $type->id() . '">' . $type->id() . '</a><br />';
      }

    }
    else {

      $paragraph_type_entity = \Drupal::entityTypeManager()->getStorage('paragraphs_type')->load($paragraph_type);

      if (empty($paragraph_type_entity)) {
        $build[] = 'There is no paragraph type ' . $paragraph_type;
      }
      else {

        $paragraphs = \Drupal::entityTypeManager()->getStorage('paragraph')->loadByProperties(['type' => $paragraph_type]);

        if (empty($paragraphs)) {
          $build[] = 'No paragraphs of type ' . $paragraph_type . ' found.';
        }
        else {

          $build[] = '<table>';
          $build[] = '<tr><th>Paragraph</th><th>Parents Tree</th>';

          /** @var \Drupal\paragraphs\ParagraphInterface $paragraph */
          foreach ($paragraphs as $paragraph) {

            $parents = [];
            $parent = $paragraph;

            while ($parent instanceof ParagraphInterface) {

              $parent = $parent->getParentEntity();

              if (!empty($parent)) {
                $entity_type = $parent->getEntityTypeId();

                if ($entity_type === 'node') {
                  $parents[] = 'node: ' . $parent->bundle() . ': <a href="/node/' . $parent->id() . '">' . $parent->label() . '</a>';
                }
                else {
                  $parents[] = $entity_type . ': ' . $parent->bundle() . ': ' . $parent->id();
                }
              }
              else {
                $parents[] = '[orphan?]';
              }
            }

            $row = '<tr>';

            // Paragraph type/ID column.
            $row .= '<td>' . $paragraph->bundle() . ': ' . $paragraph->id() . '</td>';

            // Parents column.
            $row .= '<td>';
            $indent = '';
            foreach (array_reverse($parents) as $parent_markup) {
              $row .= $indent . $parent_markup . '<br />';
              $indent .= '&nbsp;&nbsp;';
            }
            $row .= '</td></tr>';

            $build[] = $row;

          }

          $build[] = '</table>';
        }

      }
    }

    return [
      '#markup' => implode('', $build),
    ];

  }

}
