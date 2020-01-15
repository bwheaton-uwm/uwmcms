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
   *   Additional options for the command.
   *
   * @command uwm:refresh-all-binary-fountain-reviews
   *
   * @option refresh-all Flag to confirm many should be refreshed.
   * @option refresh-limit Flag to set the maximum number to update.
   *
   * @default $options []
   *
   * @usage uwm:refresh-all-binary-fountain-reviews --refresh-all=true
   *   --refresh-limit=20
   *
   * @return null
   *   No return values used.
   */
  public function refreshAllBinaryFountainReviews(array $options = []) {

    $options['refresh-all'] = $options['refresh-all'] ?? FALSE;
    $options['refresh-limit'] = $options['refresh-limit'] ?? 10;

    if (!$options['refresh-all']) {
      $this->loggerChannelFactory->get(__CLASS__)
        ->notice('We did not receive refresh-all flag so quitting early. Note a limit will still be enforced.');
      return NULL;
    }

    $result = \Drupal::entityQuery('node')
      ->condition('type', 'res_provider')
      ->condition('field_res_npi', '', '<>')
      ->sort('changed', 'ASC')
      ->execute();

    // $this->batchDrushActivity($nids);
    if (is_array($result)) {

      $nodeIds = array_slice($result, 0, $options['refresh-limit']);
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

  /**
   * Perform one or more migration processes.
   *
   * @param string $migration_names
   *   ID of migration(s) to import. Delimit multiple using commas.
   * @param array $options
   *   Additional options for the command.
   *
   * @command uwm:test
   *
   * @option all Process all migrations.
   * @option group A comma-separated list of migration groups to import
   * @option tag Name of the migration tag to import
   * @option limit Limit on the number of items to process in each migration
   * @option feedback Frequency of progress messages, in items processed
   * @option idlist Comma-separated list of IDs to import
   * @option idlist-delimiter The delimiter for records, defaults to ':'
   * @option update  In addition to processing unprocessed items from the
   *   source, update previously-imported items with the current data
   * @option force Force an operation to run, even if all dependencies are not
   *   satisfied
   * @option execute-dependencies Execute all dependent migrations first.
   *
   * @default $options []
   *
   * @usage uwm:test --all
   *   Perform all migrations
   * @usage uwm:test --group=beer
   *   Import all migrations in the beer group
   * @usage uwm:test --tag=user
   *   Import all migrations with the user tag
   * @usage uwm:test --group=beer --tag=user
   *   Import all migrations in the beer group and with the user tag
   * @usage uwm:test beer_term,beer_node
   *   Import new terms and nodes
   * @usage uwm:test beer_user --limit=2
   *   Import no more than 2 users
   * @usage uwm:test beer_user --idlist=5
   *   Import the user record with source ID 5
   * @usage uwm:test beer_node_revision --idlist=1:2,2:3,3:5
   *   Import the node revision record with source IDs [1,2], [2,3], and [3,5]
   *
   * @validate-module-enabled migrate_tools
   *
   * @aliases mim, migrate-import
   *
   * @throws \Exception
   *   If there are not enough parameters to the command.
   */
  public function test($migration_names = '', array $options = []) {
    $options += [
      'all' => NULL,
      'group' => NULL,
      'tag' => NULL,
      'limit' => NULL,
      'feedback' => NULL,
      'idlist' => NULL,
      'idlist-delimiter' => ':',
      'update' => NULL,
      'force' => NULL,
      'execute-dependencies' => NULL,
    ];
    $group_names = $options['group'];
    $tag_names = $options['tag'];
    $all = $options['all'];
    $additional_options = [];
    if (!$all && !$group_names && !$migration_names && !$tag_names) {
      throw new \Exception(dt('You must specify --all, --group, --tag or one or more migration names separated by commas'));
    }

    $possible_options = [
      'limit',
      'feedback',
      'idlist',
      'idlist-delimiter',
      'update',
      'force',
      'execute-dependencies',
    ];
    foreach ($possible_options as $option) {
      if ($options[$option]) {
        $additional_options[$option] = $options[$option];
      }
    }

    $this->loggerChannelFactory->get(__CLASS__)
      ->notice('You said @options.', [
        '@options' => print_r($options, 1),
      ]);

  }

}
