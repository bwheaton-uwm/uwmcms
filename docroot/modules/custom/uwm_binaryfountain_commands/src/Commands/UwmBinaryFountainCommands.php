<?php

namespace Drupal\uwm_binaryfountain_commands\Commands;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\KeyValueStore\KeyValueFactoryInterface;
use Drupal\Core\Logger\LoggerChannelFactoryInterface;
use Drupal\node\Entity\Node;
use Drupal\uwm_binaryfountain_reviews\Controller\UwmAttachNodeReviews;
use Drush\Commands\DrushCommands;

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
   * Key-value store service.
   *
   * @var \Drupal\Core\KeyValueStore\KeyValueFactoryInterface
   */
  protected $keyValue;

  /**
   * Constructs a new UpdateVideosStatsController object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   Entity type service.
   * @param \Drupal\Core\Logger\LoggerChannelFactoryInterface $loggerChannelFactory
   *   Logger service.
   * @param \Drupal\Core\KeyValueStore\KeyValueFactoryInterface $keyValue
   *   Key-value store service.
   */
  public function __construct(EntityTypeManagerInterface $entityTypeManager, LoggerChannelFactoryInterface $loggerChannelFactory, KeyValueFactoryInterface $keyValue) {
    parent::__construct();
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
   *
   * @option arr An option that takes multiple values.
   * @option msg Whether or not an extra message should be displayed to the
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
   *
   * @option providerNpi The provide NPI to match and update in Drupal.
   *
   * @return null
   *   No return values used.
   *
   * @throws \Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException
   * @throws \Drupal\Component\Plugin\Exception\PluginNotFoundException
   *
   * @usage uwm:refresh-binary-fountain-review 888
   *   Fetch new Binary Fountain API results for the provider having NPI 888.
   */
  public function refreshBinaryFountainReview($providerNpi = NULL) {

    if (!$providerNpi) {
      $this->loggerChannelFactory->get(__CLASS__)
        ->notice('Did not receive provider NPI (@npi) to update', [
          '@npi' => $providerNpi,
        ]);
      return NULL;
    }

    $nodes = \Drupal::entityTypeManager()->getStorage('node')
      ->loadByProperties(['field_res_npi' => $providerNpi]);

    if ($node = reset($nodes)) {

      new UwmAttachNodeReviews($node, TRUE);

      $this->loggerChannelFactory->get(__CLASS__)
        ->notice('Updating node (@nid) matching NPI (@npi).', [
          '@nid' => $node->id(),
          '@npi' => $providerNpi,
        ]);
    }
    else {

      $this->loggerChannelFactory->get(__CLASS__)
        ->warning('Could not find provider matching NPI (@npi).', [
          '@npi' => $providerNpi,
        ]);

    }

  }

  /**
   * Refresh the reviews of all providers with Binary Fountain results.
   *
   * @param array $options
   *   Additional options for the command; note the "count" must be provided.
   *
   * @command uwm:refresh-all-binary-fountain-reviews
   *
   * @option force Flag to confirm nodes should be refreshed.
   * @option count Flag to set the number to update.
   *
   * @default $options []
   *
   * @usage uwm:refresh-all-binary-fountain-reviews --refresh-all true
   *   --refresh-limit=20
   *
   * @return null
   *   No return values used.
   */
  public function refreshAllBinaryFountainReviews(array $options = ['count' => 0, 'force' => FALSE]) {

    if (!$options['force']) {
      $this->loggerChannelFactory->get(__CLASS__)
        ->notice('We did not receive "force" flag. Quitting early.');
      return NULL;
    }

    $result = \Drupal::entityQuery('node')
      ->condition('type', 'res_provider')
      ->condition('field_res_npi', '', '<>')
      ->sort('changed', 'ASC')
      ->execute();

    if (is_array($result)) {

      $nodeIds = array_slice($result, 0, $options['count']);
      $this->loggerChannelFactory->get(__CLASS__)
        ->notice('Starting refresh of these nodes: @nodeIds.', [
          '@nodeIds' => implode(', ', $nodeIds),
        ]);

      $nodes = Node::loadMultiple($nodeIds);

      foreach ($nodes as $node) {

        new UwmAttachNodeReviews($node, TRUE);

        $this->loggerChannelFactory->get(__CLASS__)
          ->notice('Updating node (@nid) reviews.', [
            '@nid' => $node->id(),
          ]);

      }

    }

  }

}
