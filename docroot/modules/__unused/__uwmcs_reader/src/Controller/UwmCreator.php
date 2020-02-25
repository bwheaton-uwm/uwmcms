<?php

namespace Drupal\uwmcs_reader\Controller;

/***
 * See https://www.drupal.org/docs/8/api/
 * routing-system/parameter-upcasting-in-routes
 *
 */
use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Language\LanguageInterface;
use Drupal\Core\Url;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * Controller routines for UWMCS JSON Reader pages.
 */
class UwmCreator extends ControllerBase {

  private $pathAlias;

  private $apiAlias;

  private $contentType;

  private $requestUri;

  private const CREATE_NEW_NODES = TRUE;

  /**
   * UwmCreator constructor.
   */
  public function __construct() {

    $this->requestUri = '/' . trim(strtolower(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH)), '/');
    $args = explode('/', $this->requestUri);

    if ($args[1] === 'bios' && !empty($args[2])) {
      $this->apiAlias = $args[2];
      $this->pathAlias = '/bios/' . $args[2];
      $this->contentType = 'uwm_provider';

    }
    elseif ($args[1] === 'locations' && !empty($args[2])) {
      $this->apiAlias = '/locations/' . $args[2];
      $this->pathAlias = $this->apiAlias;
      $this->contentType = 'uwm_clinic';

    }

  }

  /**
   * Returns a simple page.
   *
   * @return array
   *   A simple renderable array.
   */
  public function adminPage() {
    $element = [
      '#markup' => 'This page intentionally left blank.',
    ];
    return $element;
  }

  /**
   * This is a description.
   *
   * @param bool $createMissingNode
   *   Description here.
   */
  public function createMissingApiNode(bool $createMissingNode = FALSE) {

    if (!self::CREATE_NEW_NODES) {
      return;
    }

    if (!$this->isAliasValid()) {
      // Only create pages with an alias.
      return;
    }

    if ($this->isAliasTaken()) {
      // A page already exists. Nothing to do.
      return;

    }

    if ($createMissingNode) {

      $fetcher = new UwmFetcher();

      // Find the remote, API data:
      if ($this->contentType == 'uwm_provider') {

        $result = $fetcher->getProvider(['friendlyUrl' => $this->apiAlias]);

        if (empty($result->id)) {
          return;
        }
        $node = $this->createRemoteNode(
          $this->prepareProviderNode($result)
        );

      }

      elseif ($this->contentType == 'uwm_clinic') {

        $result = $fetcher->getClinic(['clinicUrl' => $this->apiAlias]);

        if (empty($result->id)) {
          return;
        }
        $node = $this->createRemoteNode(
          $this->prepareClinicNode($result)
        );

      }

      // If we created a node, redirect to it:
      if (!empty($node) && !empty($node->id())) {
        $url = Url::fromRoute('entity.node.canonical', ['node' => $node->id()]);
        $response = new RedirectResponse($url->toString());
        $response->send();

      }

    }

  }

  /**
   * This is a description.
   *
   * @param \stdClass $data
   *   Description here.
   *
   * @return array
   *   Description here.
   */
  private function prepareProviderNode(\stdClass $data) {

    // Populate defaults array.
    $settings = [
      'title' => $data->fullName,
      'field_information_manager_url' => $data->url,
    ];

    return $settings;

  }

  /**
   * This is a description.
   *
   * @param \stdClass $data
   *   Description here.
   *
   * @return array
   *   Description here.
   */
  private function prepareClinicNode(\stdClass $data) {

    $settings = [
      'title' => $data->clinicName,
      'field_information_manager_url' => $data->url,
    ];

    return $settings;

  }

  /**
   * This is a description.
   *
   * @return \Drupal\Core\Entity\EntityInterface
   *   Description here.
   */
  private function createRemoteNode(array $data) {

    $data = array_merge($data,
      [
        'changed' => REQUEST_TIME,
        'promote' => NODE_NOT_PROMOTED,
        'revision' => 1,
        'log' => '',
        'status' => NODE_PUBLISHED,
        'sticky' => NODE_NOT_STICKY,
        'type' => $this->contentType,
        'langcode' => LanguageInterface::LANGCODE_NOT_SPECIFIED,
        'path' => [
          'source' => '/node/1',
          'alias' => $this->pathAlias,
        ],
      ]
    );

    $node = entity_create('node', $data);
    $node->save();

    // Add path alias.
    // We can't do this intil after saving and having a node id.
    $path = [
      'source' => '/node/' . $node->id(),
      'alias' => $data['path']['alias'],
    ];

    $pathManager = \Drupal::service('path.alias_storage');
    $pathManager->save($path['source'], $path['alias']);

    return $node;

  }

  /**
   * Function desription.
   *
   * @return bool
   *   Description here.
   */
  private function isAliasValid() {

    if (empty($this->pathAlias)) {
      return FALSE;

    }
    if ($this->requestUri !== $this->pathAlias) {
      return FALSE;

    }

    return TRUE;

  }

  /**
   * Function desription.
   *
   * @return bool
   *   Description here.
   */
  private function isAliasTaken() {

    $pathManager = \Drupal::service('path.alias_storage');
    $alias = $pathManager->load(['alias' => $this->pathAlias]);

    if (empty($alias['pid'])) {
      return FALSE;

    }

    // Node exists if an alias does.
    return TRUE;

  }

}
