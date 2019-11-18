<?php

namespace Drupal\uwmcs_reader\Controller;

/***
 * See https://www.drupal.org/docs/8/api/
 * routing-system/parameter-upcasting-in-routes
 *
 */
use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Url;
use Httpful\Request;
use Httpful\Response;

/**
 * Controller routines for UWMCS JSON Reader pages.
 */
class UwmFetcher {

  protected static $apiUri = 'https://webservices.uwmedicine.org';

  protected static $clinicEndpoint = '/api/clinic';

  protected static $providerEndpoint = '/api/bioinformation';

  /**
   * UwmFetcher constructor.
   */
  public function __construct() {
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
   * Description here.
   *
   * @param string $apiEndpoint
   *   Description here.
   *
   * @return \stdClass
   *   Description here.
   */
  public function getUrl(string $apiEndpoint = NULL) {

    $data = $this->fetchItem($apiEndpoint);

    return $data;

  }

  /**
   * Description here.
   *
   * @param array $searchFields
   *   Description here.
   *
   * @return \stdClass
   *   Description here.
   */
  public function getProvider(array $searchFields = []) {

    $uri = $uri = self::$apiUri . self::$providerEndpoint;
    $provider = $this->findItem($searchFields, $uri);

    return $provider;

  }

  /**
   * Description here.
   *
   * @param array $searchFields
   *   Description here.
   *
   * @return \stdClass
   *   Description here.
   */
  public function getClinic(array $searchFields = []) {

    $uri = $uri = self::$apiUri . self::$clinicEndpoint;
    $clinic = $this->findItem($searchFields, $uri);

    return $clinic;

  }

  /**
   * Description here.
   *
   * @param array $searchFields
   *   Description here.
   * @param string|null $apiUri
   *   Description here.
   *
   * @return \stdClass
   *   Description here.
   */
  private function findItem(array $searchFields = [], string $apiUri = NULL) {

    foreach ($this->fetchItem($apiUri) as $dataItem) {

      foreach ($searchFields as $key => $val) {

        if (isset($dataItem->{$key}) && $dataItem->{$key} === $val) {

          // Matched an item in the collection.
          // If it has it's own endpoint, return that instead.
          if (!empty($dataItem->url)) {

            $detailView = $this->fetchItem($dataItem->url);

          }

          return empty($detailView) ? $dataItem : $detailView;

        }

      }

    }

    return new \stdClass();

  }

  /**
   * Return content for all providers.
   *
   * @return \stdClass
   *   Description here.
   */
  private function fetchItem(string $apiUri = NULL) {

    if (empty($apiUri)) {
      return new \stdClass();
    }

    $response = $this->getResponse($apiUri);
    if ($response instanceof Response) {

      if ($this->isResponseValid($response)) {
        return $response->body;
      }
      else {
        \Drupal::logger(__CLASS__)
          ->error('Missing response body from ' . $apiUri);

      }

    }

    return new \stdClass();

  }

  /**
   * Description here.
   *
   * @param string|null $apiUri
   *   Description here.
   *
   * @return \Httpful\Response|\stdClass
   *   Description here.
   */
  private function getResponse(string $apiUri = NULL) {

    if ($cache = $this->cacheGet($apiUri)) {

      return $cache->data;

    }

    try {

      // @TODO: Move unique cases to a request builder.
      if (stripos($apiUri, 'api/publications')) {

        // Extract JSONP and prepare JSON.
        $data = Request::get($apiUri)
          ->parseWith(function ($body) {

            if ($body[0] !== '[' && $body[0] !== '{') {
              $body = substr($body, strpos($body, '('));
            }
            return json_decode(trim($body, '();'));

          })
          ->send();

      }
      else {

        $data = Request::get($apiUri)
          ->expectsJson()
          ->send();

        $this->cacheSet($apiUri, $data);

      }

      return $data;

    }
    catch (\Exception $e) {

      \Drupal::logger(__CLASS__)
        ->error('Unable to parse ' . $apiUri . $e->getMessage());

      return new \stdClass();

    }

  }

  /**
   * Description here.
   *
   * @param string $key
   *   Description here.
   *
   * @return false|object
   *   Description here.
   */
  private function cacheGet(string $key = NULL) {

    $key = $this->cacheName($key);

    return \Drupal::cache()->get($key, FALSE);

  }

  /**
   * Description here.
   *
   * @param string $key
   *   Description here.
   * @param \Httpful\Response $data
   *   Description here.
   */
  private function cacheSet(string $key, Response $data) {

    $cid = $this->cacheName($key);
    $tags = $this->cacheTags($key);

    \Drupal::cache()
      ->set($cid, $data, CacheBackendInterface::CACHE_PERMANENT, $tags);

  }

  /**
   * Description here.
   *
   * @param string|null $dataUniqueUri
   *   Description here.
   *
   * @return mixed
   *   Description here.
   */
  private function cacheName(string $dataUniqueUri = NULL) {

    $key = $dataUniqueUri;

    return preg_replace("/[^A-Za-z0-9]/", '_', $key);

  }

  /**
   * Description here.
   *
   * @param string|null $key
   *   Description here.
   *
   * @return array
   *   Description here.
   */
  private function cacheTags(string $key = NULL) {

    $tags = ['uwm_fetcher'];

    $node = \Drupal::routeMatch()->getParameter('node');
    if ($node && method_exists($node, 'getCacheTags')) {
      $tags = array_merge($tags, $node->getCacheTags());
      $tag[] = 'uwm_fetcher_node:' . $node->id();
    }

    $current_url = Url::fromRoute('<current>');
    if ($current_url && method_exists($current_url, 'toString')) {
      $tags[] = 'uwm_fetcher_route:' . $current_url->toString();
    }

    $parsed_key = parse_url($key);
    if ($parsed_key && !empty($parsed_key['path'])) {
      $tags[] = 'uwm_fetcher_api_route:' . $parsed_key['path'];
    }

    return $tags;

  }

  /**
   * Description here.
   *
   * @param \Httpful\Response|null $response
   *   Description here.
   *
   * @return bool
   *   Description here.
   */
  private function isResponseValid(Response $response = NULL) {

    if (empty($response)) {
      return FALSE;
    }
    if (empty($response->body)) {
      return FALSE;
    }
    if (!is_object($response->body) && !is_array($response->body)) {
      return FALSE;
    }
    if (!empty($response->body->message)) {
      if (stripos($response->body->message, 'request is invalid.')) {
        return FALSE;
      }
    }
    if (!empty($response->code) && $response->code != 200) {
      return FALSE;
    }

    return TRUE;

  }

}
