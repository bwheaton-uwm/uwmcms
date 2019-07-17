<?php

namespace Drupal\uwmcs_searchfields\Plugin\search_api\Processor;

use Drupal\node\NodeInterface;
use Drupal\node\Entity\Node;
use Drupal\search_api\Datasource\DatasourceInterface;
use Drupal\search_api\Item\ItemInterface;
use Drupal\search_api\Processor\ProcessorPluginBase;
use Drupal\search_api\Processor\ProcessorProperty;

/**
 * Provides a value to the Search API for the "Search within" drop-down.
 *
 * The value is based on different properties for a content item, such as its
 * node-type, its path alias, terms associated with the node and so on. Allows
 * us to create a custom general content type category.
 *
 * @SearchApiProcessor(
 *   id = "UwmSearchWithin",
 *   label = @Translation("UwmSearchWithin"),
 *   description = @Translation("Provides additional UWM values to search."),
 *   stages = {
 *     "add_properties" = 0,
 *   },
 *   locked = true,
 *   hidden = false,
 * )
 */
class UwmSearchWithin extends ProcessorPluginBase {

  const FACET_NAME = 'UwmSearchWithin';

  const FIELD_NAME = '';

  const VALUE_PROVIDER = 'Providers';

  const VALUE_LOCATION = 'Locations';

  const VALUE_MEDICAL_SPECIALTIES = 'Medical Specialties';

  const VALUE_PATIENT_RESOURCES = 'Patient Info and Resources';

  const VALUE_EDUCATION = 'School of Medicine';

  const VALUE_RESEARCH = 'Research';

  const VALUE_REFERRALS = 'Referrals';

  /**
   * {@inheritdoc}
   */
  public function getPropertyDefinitions(DatasourceInterface $datasource = NULL) {

    $properties = [];

    if (!$datasource) {

      $definition = [
        'label' => self::FACET_NAME,
        'description' => $this->t('General area or type of content item.'),
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
    if ($entity instanceof NodeInterface) {

      // $data = UwmSearchUtils::extractAllApiMatches($node, self::FIELD_NAME);.
      $newValues = [];

      self::setValueForContentTypes($entity, $newValues);
      self::setValueForContentTerms($entity, $newValues);
      self::setValueForContentAlias($entity, $newValues);

      $itemFields = $item->getFields(FALSE);
      $itemFields = $this->getFieldsHelper()
        ->filterForPropertyPath($itemFields, NULL,
          strtolower(self::FACET_NAME));

      foreach ($newValues as $value) {
        if (is_string($value) && !empty($value)) {
          foreach ($itemFields as $field) {
            $field->addValue($value);
          }
        }
      }

    }

  }

  /**
   * Description here.
   *
   * @param \Drupal\node\Entity\Node $node
   *   Description here.
   * @param array $matches
   *   Description here.
   */
  private function setValueForContentTypes(Node $node, array &$matches) {

    $nodeType = $node->getType();

    if ($nodeType === 'res_provider') {
      $matches[] = self::VALUE_PROVIDER;
    }

    if ($nodeType === 'res_clinic') {
      $matches[] = self::VALUE_LOCATION;
    }

    if ($nodeType === 'uwm_hub_page') {
      $matches[] = self::VALUE_PATIENT_RESOURCES;
    }

    if ($nodeType === 'uwm_medical_service' ||
        $nodeType === 'uwm_medical_specialty') {
      $matches[] = self::VALUE_MEDICAL_SPECIALTIES;
    }
  }

  /**
   * Description here.
   *
   * @param \Drupal\node\Entity\Node $node
   *   Description here.
   * @param array $matches
   *   Description here.
   */
  private function setValueForContentTerms(Node $node, array &$matches) {
  }

  /**
   * Description here.
   *
   * @param \Drupal\node\Entity\Node $node
   *   Description here.
   * @param array $matches
   *   Description here.
   */
  private function setValueForContentAlias(Node $node, array &$matches) {

    $alias = \Drupal::service('path.alias_manager')
      ->getAliasByPath('/node/' . $node->nid->value);

    if (!empty($alias)) {

      if (stripos($alias, 'education') !== FALSE ||
          stripos($alias, 'school') !== FALSE) {
        $matches[] = self::VALUE_EDUCATION;
      }

      if (stripos($alias, 'research') !== FALSE) {
        $matches[] = self::VALUE_RESEARCH;
      }

      if (stripos($alias, 'provider-resource') !== FALSE) {
        $matches[] = self::VALUE_REFERRALS;
      }

      if (stripos($alias, 'about') !== FALSE ||
          stripos($alias, 'patient-care') !== FALSE ||
          stripos($alias, 'patient-resources') !== FALSE
      ) {
        $matches[] = self::VALUE_PATIENT_RESOURCES;
      }

      if (stripos($alias, 'services/') !== FALSE ||
          stripos($alias, 'specialties/') !== FALSE ||
          stripos($alias, 'specialty/') !== FALSE) {
        $matches[] = self::VALUE_MEDICAL_SPECIALTIES;
      }

    }

  }

}
