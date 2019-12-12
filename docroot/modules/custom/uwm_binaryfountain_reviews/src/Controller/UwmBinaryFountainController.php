<?php

namespace Drupal\uwm_binaryfountain_reviews\Controller;

/**
 * Simple class for Binary Fountain ratings and reviews.
 *
 * We bundle getting Binary Fountain responses here so functionality is in
 * one place and also, so themes can get reviews via AJAX, after pages load
 * or with a hook before page rendering. Note Binary Fountain API calls require
 * a secret access app_id and app_secret.
 *
 * @code
 * $ curl --data "appId=776743546280515&appSecret=89ff3ebe-700a-474c-8ca0-
 * 6010ed0db28d"  https://api.binaryfountain.com/api/service/v1/token/create
 * {"expiresIn":"2019-10-01 17:44:46","accessToken":"SaaPigG4bkMdxXx3EFy5_
 *
 * $ TOKEN='SaaPigG4bkMdxXx3EFy5_1569950086306'
 *
 * $ curl -H "accessToken: $TOKEN" https://api.binaryfountain.com/api/service/
 * bsr/comments\?personId\=1001111111
 * {"status":{"message":"success","code":200},"data":{"client":"Baystate"
 *
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

  /**
   * Response for route from routing yml.
   *
   * @param \Symfony\Component\HttpFoundation\Request $request
   *   Description here.
   *
   * @return \Drupal\Component\Serialization\JsonResponse
   *   Description here.
   */
  public function getExample(Request $request) {

    $response['data'] = 'Some test data to return';
    $response['xyz'] = [1234];

    return new JsonResponse($response);

  }

  /**
   * Endpoint with Binary Fountain provider ratings.
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

    $this->initSettings();

    if ($providerNpi) {

      try {
        $response = $this->fetchRatings($providerNpi);

      }
      catch (\Exception $e) {
        \Drupal::logger(__CLASS__)->error($e->getMessage());
        return new JsonResponse([]);
      }
      return new JsonResponse($response);

    }

    return new JsonResponse([]);

  }

  /**
   * Initialize settings needed for the 3rd party service.
   */
  private function initSettings() {

    $uwm_container_settings = \Drupal::config('uwm_binaryfountain_reviews.binary_fountain');
    $this->binaryFountainAppId = $uwm_container_settings->get('values.BINARY_FOUNTAIN_APP_ID');
    $this->binaryFountainAppSecret = $uwm_container_settings->get('values.BINARY_FOUNTAIN_APP_SECRET');

  }

  /**
   * Get an non-expired token for Binary Fountain API calls.
   */
  private function fetchToken() {

    if (!$this->isValidToken()) {

      $client = \Drupal::httpClient();
      $request = $client->post('https://api.binaryfountain.com/api/service/v1/token/create', [
        'form_params' => [
          'appId' => $this->binaryFountainAppId,
          'appSecret' => $this->binaryFountainAppSecret,
        ],
      ]);

      // Check Drupal Guzzle success:
      if ($request->getStatusCode() == 200) {

        // @TODO: Validate JSON?
        $responseBody = $request->getBody()->getContents();
        $responseBodyDecoded = Json::decode($responseBody);

        // Check api.binaryfountain.com success:
        if (!empty($responseBodyDecoded['accessToken']) && $responseBodyDecoded['status']['code'] == 200) {

          $this->binaryFountainToken = $responseBodyDecoded['accessToken'];
          $this->binaryFountainTokenExpires = $responseBodyDecoded['expiresIn'];

        }

      }

    }

    if ($this->isValidToken()) {
      return $this->binaryFountainToken;
    }

    return NULL;

  }

  /**
   * Fetch the provider ratings and comments JSON from Binary Fountain.
   */
  private function fetchRatings(int $personId = NULL) {

    $client = \Drupal::httpClient();
    $query = [
      'personId' => $personId,
    ];
    $headers = [
      'accessToken' => $this->fetchToken(),
    ];
    $request = $client->get('https://api.binaryfountain.com/api/service/bsr/comments', [
      'headers' => $headers,
      'query' => $query,
    ]);

    // @TODO: Validate JSON?
    $responseBody = $request->getBody()->getContents();
    $responseBodyDecoded = Json::decode($responseBody);

    // Check api.binaryfountain.com success:
    if (!empty($responseBodyDecoded['status']['code']) && $responseBodyDecoded['status']['code'] == 200) {
      return $responseBodyDecoded;
    }

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
