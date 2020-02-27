<?php

namespace Drupal\uwmcs_searchfields\Plugin\search_api\Processor;

use Drupal\search_api\Datasource\DatasourceInterface;
use Drupal\search_api\Item\ItemInterface;
use Drupal\search_api\Processor\ProcessorPluginBase;
use Drupal\search_api\Processor\ProcessorProperty;
use Drupal\uwmcs_searchfields\Controller\UwmSearchInfoMgrHelper;

/**
 * Adds the item's URL to the indexed data.
 *
 * @SearchApiProcessor(
 *   id = "ImClinicServices",
 *   label = @Translation("ImClinicServices"),
 *   description = @Translation("Provides additional UWM values to search."),
 *   stages = {
 *     "add_properties" = 0,
 *   },
 *   locked = true,
 *   hidden = false,
 * )
 */
class ImClinicServices extends ProcessorPluginBase {

  const FACET_NAME = 'ImClinicServices';

  const FIELD_NAME = 'serviceName';

  /**
   * {@inheritdoc}
   */
  public function getPropertyDefinitions(DatasourceInterface $datasource = NULL) {

    $properties = [];

    if (!$datasource) {

      $definition = [
        'label' => self::FACET_NAME,
        'description' => $this->t('Provider extra values for search.'),
        'type' => 'string',
        'processor_id' => $this->getPluginId(),
      ];

      $properties[strtolower(self::FACET_NAME)] = new ProcessorProperty($definition);

    }

    return $properties;

  }

  /**
   * {@inheritdoc}
   */
  public function addFieldValues(ItemInterface $item) {

    $entity = $item->getOriginalObject(TRUE)->getValue();
    if ($entity->getType() === 'uwm_clinic') {

      $data = UwmSearchInfoMgrHelper::getEntityApiData($entity);
      $newValues = UwmSearchInfoMgrHelper::extractAllApiMatches($data, self::FIELD_NAME);
      $newValues = $newValues + UwmSearchInfoMgrHelper::extractAllApiMatches($data, 'lineOfCareName');

      $fields = $item->getFields(FALSE);
      $fields = $this->getFieldsHelper()
        ->filterForPropertyPath($fields, NULL,
          strtolower(self::FACET_NAME));

      foreach ($newValues as $value) {
        foreach ($fields as $field) {
          if (!empty($value)) {
            $field->addValue($value);
          }
        }
      }

    }

  }

}
