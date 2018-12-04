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
 *   id = "ImBioLanguage",
 *   label = @Translation("ImBioLanguage"),
 *   description = @Translation("Provides additional UWM values to search."),
 *   stages = {
 *     "add_properties" = 0,
 *   },
 *   locked = true,
 *   hidden = false,
 * )
 */
class ImBioLanguage extends ProcessorPluginBase {

  const FACET_NAME = 'ImBioLanguage';

  const FIELD_NAME = 'languageName';

  /**
   * {@inheritdoc}
   */
  public function getPropertyDefinitions(DatasourceInterface $datasource = NULL) {

    $properties = [];

    if (!$datasource) {

      $definition = [
        'label' => self::FACET_NAME,
        'description' => $this->t('Provider expertises from the Information Manager(IM) API'),
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

    // $item->itemId == 'entity:node/10006:und';.
    $entity = $item->getOriginalObject(TRUE)->getValue();
    if ($entity->getType() === 'uwm_provider') {

      $data = UwmSearchInfoMgrHelper::getEntityApiData($entity);
      $newValues = UwmSearchInfoMgrHelper::extractAllApiMatches($data, self::FIELD_NAME);

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
