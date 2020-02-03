<?php

namespace Drupal\uwm_binaryfountain_reviews\Controller;

use Drupal\Component\Serialization\Json;
use Drupal\node\Entity\Node;
use Drupal\node\NodeInterface;

/**
 * UwmAttachNodeReviews class to attach reviews to node field.
 */
class UwmAttachNodeReviews {

  const NODE_DYNAMIC_REVIEWS_FIELD = 'uwm_binaryfountain_reviews';

  const REVIEWS_DATE_REFRESHED_FIELD = 'uwm_ratings_refreshed_date';

  protected $node = NULL;

  /**
   * UwmAttachNodeReviews constructor.
   *
   * Attaches ratings JSON to the node as a PHP object.
   *
   * @param \Drupal\node\NodeInterface $node
   *   The node from a node_load to attach review to.
   */
  public function __construct(NodeInterface &$node) {

    /***
     * Create a dynamic property for modules or theming. It will contain the
     * the Binary Fountain API response for a given provider (as a live PHP
     * object).
     */
    if ($node->hasField('field_ratings')
      && $node->hasField('field_res_npi')) {

      $this->node =& $node;
      $this->setRatings(
            $this->node->field_ratings->value
      );

    }

  }

  /**
   * Gets BinaryFountain ratings if they're stale or a refresh was requested.
   *
   * @param bool $forceRefresh
   *   Force a refresh, instead of refreshing once per 24 hrs.
   */
  public function getRatings($forceRefresh = FALSE) {

    if (!$this->validateRatings() || $forceRefresh) {

      $this->refreshRatings();

    }

  }

  /**
   * Attach a PHP review object to the node for theming.
   */
  private function setRatings(string $rawRatings = NULL) {

    if ($rawRatings && ($decodedRatings = Json::decode($rawRatings))) {

      $this->node->{self::NODE_DYNAMIC_REVIEWS_FIELD} = $decodedRatings;

    }
    else {

      $this->node->{self::NODE_DYNAMIC_REVIEWS_FIELD} = [];

    }

  }

  /**
   * Is our Binary Fountain data valid.
   *
   * Check the Binary Fountain ratings data. Ensure we have a `data` property,
   * altho not if it's populated, and that the reviews fetch is less than a
   * day old.
   *
   * @return bool
   *   TRUE if valid Binary Fountain reviews response.
   */
  private function validateRatings() {

    $reviews = $this->node->{self::NODE_DYNAMIC_REVIEWS_FIELD};
    $dateFetched = $reviews[self::REVIEWS_DATE_REFRESHED_FIELD] ?? NULL;

    if (!$reviews || empty($reviews['data']) || empty($dateFetched)) {
      return FALSE;
    }

    if (!$dateFetched || strtotime('-24 hours') >= strtotime($dateFetched)) {
      return FALSE;
    }

    return TRUE;

  }

  /**
   * Saves newest provider reviews to the node, if outdated.
   */
  private function refreshRatings() {

    $fetcher = new UwmBinaryFountainController();
    $reviews = $fetcher->getProviderBinaryFountainReviews(
        $this->node->field_res_npi->value
      ) ?? [];

    /***
     * Ensure we store a date fetched, at a minimum, so we're not refreshing
     * continously for providers missing data.
     */
    $reviews[self::REVIEWS_DATE_REFRESHED_FIELD] = date('r');

    $node = Node::load($this->node->id());
    $node->set('field_ratings', Json::encode($reviews));
    $node->save();

    // Refresh live field so we don't need to reload the node.
    $this->setRatings(
      $node->field_ratings->value
    );

  }

  /**
   * Clears reviews data, if missing.
   */
  private function removeRatings() {

    // @TODO: Empty the provider ratings. Is this needed since we check daily?

  }

}
