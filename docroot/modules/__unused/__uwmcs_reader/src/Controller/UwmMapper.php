<?php

namespace Drupal\uwmcs_reader\Controller;

/**
 * Controller connects API fields to Drupal fields.
 */
class UwmMapper {

  /**
   * Description here.
   *
   * @param string|null $apiFieldValue
   *   Description here.
   * @param string|null $nodePathAlias
   *   Description here.
   *
   * @return \Drupal\Core\Entity\EntityInterface|mixed|null|static
   *   Description here.
   */
  public function loadApiNode(string $apiFieldValue = NULL, string $nodePathAlias = NULL) {

    if (!empty($apiFieldValue)) {

      // @TODO: Trim search, in case url it http, https, etc.
      $nodes = \Drupal::entityTypeManager()
        ->getStorage('node')
        ->loadByProperties(['field_information_manager_url' => $apiFieldValue]);

      if ($node = reset($nodes)) {

        return $node;

      }

    }

  }

  /**
   * Find a node Id based on it's URL path.
   *
   * @code
   * \Drupal\uwmcs_reader\Controller\UwmMapper
   *   ::getNidByPathAlias('/locations/urgent-care-federal-way/');
   * @endcode
   *
   * @param string $pathAlias
   *   Value in node page's URL PATH SETTINGS.
   *
   * @return int|null
   *   Node id, if found.
   */
  public static function getNidByPathAlias(string $pathAlias) {

    $alias = '/' . trim($pathAlias, ' \/');
    $path = \Drupal::service('path.alias_manager')
      ->getPathByAlias($alias);
    if (preg_match('/node\/(\d+)/', $path, $matches)) {

      // Return Node::load($matches[1]);.
      return $matches[1];

    }

  }

  /**
   * Find a node path based on it's nid.
   *
   * @code
   * \Drupal\uwmcs_reader\Controller\UwmMapper
   *   ::getPathByNid(201);
   * @endcode
   *
   * @param int $nodeId
   *   The node's internal id.
   *
   * @return string|null
   *   Path alias, if found.
   */
  public static function getPathByNid(int $nodeId) {

    $path = \Drupal::service('path.alias_manager')
      ->getAliasByPath('/node/' . $nodeId);

    return $path;

  }

  /**
   * Find a node Id based on IM-API address.
   *
   * This value is stored on nodes and corresponds
   * to Information Manager endpoint having data for
   * clinic, provider, expertise, etc.
   *
   * @code
   * \Drupal\uwmcs_reader\Controller\UwmMapper
   *   ::getNidByInformationManagerUri('api/clinic/7664');
   * @endcode
   *
   * @param string $informationManagerUri
   *   Trailing portion of Information Manager endpoint,
   *   having this node's data.
   *
   * @return int|null
   *   Node id, if found.
   */
  public static function getNidByInformationManagerUri(string $informationManagerUri) {

    $apiPath = parse_url($informationManagerUri, PHP_URL_PATH);
    $query = \Drupal::entityQuery('node')
      // ->condition('status', 1)
      ->condition('field_information_manager_url', $apiPath, 'ENDS_WITH');
    $nids = $query->execute();

    if (!empty($nids) && is_array($nids)) {
      $values = array_values($nids);
      return array_shift($values);
    }

    // $node = entity_load('node', $nids[1]);.
  }

}
