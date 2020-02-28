<?php

namespace Drupal\uwm_commands\Commands;

use Drupal\Component\Plugin\PluginManagerInterface;
use Drupal\Core\Database\Connection;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\KeyValueStore\KeyValueFactoryInterface;
use Drush\Commands\DrushCommands;

// Use Drupal\uwm_commands\Commands\UwmTempImportClinicImage;.
/**
 * Define Drush commands for UW Medicine.
 */
class UwmCommands extends DrushCommands {

  /**
   * The migration plugin manager.
   *
   * @var \Drupal\Component\Plugin\PluginManagerInterface
   */
  protected $migrationPluginManager;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The migrate last imported store.
   *
   * @var \Drupal\Core\KeyValueStore\KeyValueStoreInterface
   */
  protected $migrateLastImportedStore;

  /**
   * The database connection.
   *
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * UwmCommands constructor.
   *
   * @param \Drupal\Component\Plugin\PluginManagerInterface $migration_plugin_manager
   *   The migration plugin manager.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   * @param \Drupal\Core\KeyValueStore\KeyValueFactoryInterface $key_value_factory
   *   The key/value factory.
   * @param \Drupal\Core\Database\Connection $database
   *   The database connection.
   */
  public function __construct(PluginManagerInterface $migration_plugin_manager, EntityTypeManagerInterface $entity_type_manager, KeyValueFactoryInterface $key_value_factory, Connection $database) {
    $this->migrationPluginManager = $migration_plugin_manager;
    $this->entityTypeManager = $entity_type_manager;
    $this->migrateLastImportedStore = $key_value_factory->get('migrate_last_imported');
    $this->database = $database;
  }

  /**
   * Clear the last imported timestamp for the specified migration.
   *
   * @param string $migration
   *   The migration ID.
   *
   * @command uwm:migration:clear-last-imported
   *
   * @usage uwm:migration:clear-last-imported uwm_res_import_providers
   *   Clear the last imported timestamp for the Import Providers migration.
   */
  public function migrationClearLastImported($migration) {
    $this->migrateLastImportedStore->delete($migration);
  }


}
