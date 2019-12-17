<?php

namespace Drupal\uwm_binaryfountain_commands\Commands;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\uwm_binaryfountain_reviews\Controller\UwmAttachNodeReviews;
use Drush\Commands\DrushCommands;
use Drupal\node\Entity\Node;

/**
 * A Drush uwm_binaryfountain_commands command.
 *
 * @see https://docs.drush.org/en/master/commands/
 * @see https://www.axelerant.com/resources/team-blog/how-to-write-custom-drush-9-commands-for-drupal-8
 * @see http://karimboudjema.com/en/drupal/20181127/how-run-batch-processing-custom-drush-9-command-drupal-8
 */
class UwmBinaryFountainCommands extends DrushCommands {

  /**
   * Entity type service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  private $entityTypeManager;

  /**
   * Logger service.
   *
   * @var \Drupal\Core\Logger\LoggerChannelFactoryInterface
   */
  private $loggerChannelFactory;

  /**
   * Constructs a new UpdateVideosStatsController object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   Entity type service.
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $loggerChannelFactory
   *   Logger service.
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager, LoggerChannelFactoryInterface $loggerChannelFactory) {
    $this->entityTypeManager = $entityTypeManager;
    $this->loggerChannelFactory = $loggerChannelFactory;
  }

  /**
   * Echos back hello with the argument provided.
   *
   * @param string $name
   *   Argument provided to the drush command.
   * @param array $options
   *   Additional optional options.
   *
   * @command uwm:hello
   * @aliases uwm
   * @options arr An option that takes multiple values.
   * @options msg Whether or not an extra message should be displayed to the
   *   user.
   * @usage uwm:hello akanksha --msg
   *   Display 'Hello Akanksha!' and a message.
   */
  public function hello($name, array $options = ['msg' => FALSE]) {
    if ($options['msg']) {
      $this->output()
        ->writeln('Hello ' . $name . ' and a message.');
    }
    else {
      $this->output()->writeln('Hello ' . $name . '!');
    }
  }

  /**
   * Refresh the reviews of a provider with latest from Binary Fountain.
   *
   * @param string $providerNpi
   *   The provide NPI to match and update in Drupal.
   *   Argument provided to the drush command.
   *
   * @command uwm:refresh-binary-fountain-review
   * @options providerNpi The provide NPI to match and update in Drupal.
   *
   * @usage uwm:refresh-binary-fountain-review 888
   *   Fetch new Binary Fountain API results for the provider having NPI 888.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   */
  public function refreshBinaryFountainReview($providerNpi = NULL) {

    if (!$providerNpi) {
      $this->loggerChannelFactory->get(__CLASS__)
        ->notice('@class did not receive provider NPI (@npi) to update', [
          '@class' => __CLASS__,
          '@npi' => $providerNpi,
        ]);
    }

    $nodes = \Drupal::entityTypeManager()
      ->getStorage('node')
      ->loadByProperties(['field_res_npi' => $providerNpi]);

    if ($node = reset($nodes)) {

      new UwmAttachNodeReviews($node, TRUE);

      $this->loggerChannelFactory->get(__CLASS__)
        ->notice('@class Updating node (@nid) matching NPI (@npi).', [
          '@class' => __CLASS__,
          '@nid' => $node->id(),
          '@npi' => $providerNpi,
        ]);
    }
    else {

      $this->loggerChannelFactory->get(__CLASS__)
        ->warning('@class could not find provider matching NPI (@npi).', [
          '@class' => __CLASS__,
          '@npi' => $providerNpi,
        ]);

    }

  }

  /**
   * Refresh the reviews of all providers with Binary Fountain results.
   *
   * @param array $options
   *   An array of options for the command.
   *
   * @command uwm:refresh-all-binary-fountain-reviews
   * @aliases uwm-refresh-all-bf
   *
   * @options array $options
   * @options refresh-all Flag to confirm many should be refreshed.
   * @options refresh-limit Flag to set the maximum number to update.
   *
   * @usage uwm:refresh-all-binary-fountain-reviews --refresh-all true --refresh-limit 20
   *   providerNpi is the National Provider Id of a single
   */
  public function refreshAllBinaryFountainReviews(array $options = [
    'refresh-all' => FALSE,
    'refresh-limit' => 10,
  ]) {

    $result = \Drupal::entityQuery('node')
      ->condition('type', 'res_provider')
      ->condition('field_res_npi', '', '<>')
      ->sort('changed', 'ASC')
      ->execute();

    // $this->batchDrushActivity($nids);
    if (is_array($result)) {

      $nodeIds = array_slice($result, 0, $options['refresh-limit']);
      $this->loggerChannelFactory->get(__CLASS__)
        ->notice('@class Starting refresh of these nodes: @nodeIds.', [
          '@class' => __CLASS__,
          '@nodeIds' => implode(', ', $nodeIds),
        ]);

      $nodes = Node::loadMultiple($nodeIds);

      foreach ($nodes as $node) {

        new UwmAttachNodeReviews($node, TRUE);

        $this->loggerChannelFactory->get(__CLASS__)
          ->notice('@class Updating node (@nid) reviews.', [
            '@class' => __CLASS__,
            '@nid' => $node->id(),
          ]);

      }

    }

  }

}
