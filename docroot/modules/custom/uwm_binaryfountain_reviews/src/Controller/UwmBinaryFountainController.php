<?php

namespace Drupal\uwm_binaryfountain_reviews\Controller;

/**
 * Simple class for Binary Fountain ratings and reviews.
 *
 * We bundle getting Binary Fountain responses here so functionality is in
 * one place so themes can get reviews via AJAX, after pages load or with
 * a hook before page rendering. Note Binary Fountain API calls require
 * a secret access app_id and app_secret.
 *
 * @code
 * $ curl --data "appId=0000000"
 *   https://api.binaryfountain.com/api/service/v1/token/create
 * {"expiresIn":"2019-10-01 17:44:46","accessToken":"Saaa_
 *
 * $ curl -H "accessToken: $TOKEN" https://api.binaryfountain.com/api/service/
 * bsr/comments\?personId\=$ID
 * {"status":{"message":"success","code":200},"data":{"client":"Baystate"...
 *
 * @see http://docs.guzzlephp.org/en/stable/psr7.html
 * @see https://drupalize.me/blog/201512/speak-http-drupal-httpclient
 */

use Drupal\Component\Serialization\Json;
use Drupal\Core\Controller\ControllerBase;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller methods for uwm_rest_resource.routing.yml routes.
 */
class UwmBinaryFountainController extends ControllerBase {

  private $binaryFountainAppId = NULL;

  private $binaryFountainAppSecret = NULL;

  private $binaryFountainToken = NULL;

  private $binaryFountainTokenExpires = NULL;

  const TOKEN_CACHE_KEY_NAME = 'UwmBinaryFountainControllerToken';

  /**
   * UwmBinaryFountainController constructor.
   */
  public function __construct() {

    // Fetch a token for later requests.
    $this->initSettings();

  }

  /**
   * Endpoint for viewing ratings and reviews json from Binary Fountain.
   *
   * @param string $providerNpi
   *   Provider id to fetch from Binary Fountain.
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   Drupal request object.
   *
   * @return \Drupal\Component\Serialization\JsonResponse
   *   Binary Fountain JSON, or an empty array.
   */
  public function reviewsResponse(string $providerNpi, Request $request) {

    $response = $this->getProviderBinaryFountainReviews($providerNpi);
    return new JsonResponse($response);

  }

  /**
   * Fetches the comments and ratings for a given provider from Binary Fountain.
   *
   * @param string|null $providerNpi
   *   Provider id to fetch from Binary Fountain.
   *
   * @return array
   *   An array containing the API response, or empty array if none.
   */
  public function getProviderBinaryFountainReviews(string $providerNpi = NULL) {

    if ($providerNpi && $this->binaryFountainAppId) {

      /***
       * In our fetch-token and fetch-reviews methods below we
       * throw an exception if getting a bad response. Exceptions are
       * try/ catch statements are expensive, but let's use them to
       * allow creating a trace, and also so we can always return
       * [] for the remote API.
       */
      try {

        $response = $this->fetchRatings($providerNpi);

      }
      catch (\Exception $e) {
        \Drupal::logger(__FUNCTION__)
          ->error($e->getMessage() . $e->getTraceAsString());
        return [];
      }

      return $response;

    }

    return [];

  }

  /**
   * Init settings needed for using the API.
   */
  private function initSettings() {

    $config = \Drupal::config('uwm_binaryfountain_reviews.binary_fountain');
    $this->binaryFountainAppId = $config->get('values.BINARY_FOUNTAIN_APP_ID');
    $this->binaryFountainAppSecret = $config->get('values.BINARY_FOUNTAIN_APP_SECRET');

  }

  /**
   * Fetch the provider ratings and comments JSON from Binary Fountain.
   *
   * @param int|null $personId
   *   The National Provier Identifier or Binary Fountain person Id.
   *
   * @return array
   *   The Binary Fountain reviews data or, an empty array.
   *
   * @throws \Exception
   *   Exception thrown for bad response or missing response data attribute.
   */
  private function fetchRatings(int $personId = NULL) {

    $client = \Drupal::httpClient();

    $request = $client->get('https://api.binaryfountain.com/api/service/bsr/comments', [
      'headers' => [
        'accessToken' => $this->fetchToken(),
      ],
      'query' => [
        'personId' => $personId,
      ],
    ]);

    // Check Guzzle response status:
    if ($request && $request->getStatusCode() == 200) {

      $responseBody = $request->getBody()->getContents();
      if ($responseBody
        && ($responseBodyDecoded = Json::decode($responseBody))
        && !empty($responseBodyDecoded['data'])) {

        return (array) $responseBodyDecoded;

      }
      else {
        throw new \Exception(__FUNCTION__ . ' Missing Binary Fountain response for NPI ' . $personId);
      }

    }

    return [];

  }

  /**
   * Get an non-expired token for Binary Fountain API calls.
   */
  private function fetchToken() {

    if ($cache = \Drupal::cache()->get(self::TOKEN_CACHE_KEY_NAME)) {
      $this->binaryFountainToken = $cache->data['accessToken'] ?? NULL;
      $this->binaryFountainTokenExpires = $cache->data['expiresIn'] ?? NULL;
    }

    if (!$this->isValidToken()) {

      $client = \Drupal::httpClient();
      $request = $client->post('https://api.binaryfountain.com/api/service/v1/token/create', [
        'form_params' => [
          'appId' => $this->binaryFountainAppId,
          'appSecret' => $this->binaryFountainAppSecret,
        ],
      ]);

      // Check Guzzle response status:
      if ($request && $request->getStatusCode() == 200) {

        $responseBody = $request->getBody()->getContents();
        if ($responseBody
          && ($responseBodyDecoded = Json::decode($responseBody))
          && !empty($responseBodyDecoded['accessToken'])) {

          /***
           * Our controller get re-initialized per node, or seems to,
           * by dependency injection, rather than loading once per thread.
           * Let;'s cache the results to avoid extra API calls and to reuse
           * the token almost expiring.
           */
          \Drupal::cache()
            ->set(self::TOKEN_CACHE_KEY_NAME, $responseBodyDecoded);

          $this->binaryFountainToken = $responseBodyDecoded['accessToken'];
          $this->binaryFountainTokenExpires = $responseBodyDecoded['expiresIn'];

        }
        else {
          throw new \Exception(__FUNCTION__ . ' Missing Binary Fountain token response');
        }
      }
    }

    if ($this->isValidToken()) {
      return $this->binaryFountainToken;
    }

    return NULL;

  }

  /**
   * Validate we have a token and it has not expired.
   *
   * @return bool
   *   True if we have a valid token.
   */
  private function isValidToken() {

    if (!empty($this->binaryFountainToken)) {

      $now = strtotime("now");
      $expires = strtotime($this->binaryFountainTokenExpires);

      if (($now + 60 * 10) < $expires) {
        return TRUE;
      }

    }

    return FALSE;

  }

}
