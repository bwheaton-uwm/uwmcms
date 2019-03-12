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

  /**
   * Update any content that is missing in a migration.
   *
   * @param string $migration
   *   The migration ID.
   * @param array $options
   *   Additional options for the command.
   *
   * @command uwm:migration:update-missing-content
   *
   * @option delete Delete content, instead of unpublishing it
   *
   * @default $options []
   *
   * @usage uwm:migration:update-missing-content uwm_res_import_providers
   *   Unpublish content missing in the Import Providers migration.
   * @usage uwm:migration:update-missing-content uwm_res_import_providers
   *   --delete Delete content missing in the Import Providers migration.
   */
  public function migrationUpdateMissingContent($migration, array $options = []) {
    $options += [
      'delete' => FALSE,
    ];

    switch ($migration) {
      case 'uwm_res_import_clinics3':
        $entity_type = 'node';
        $bundle = 'res_clinic';
        break;

      case 'uwm_res_import_providers3':
        $entity_type = 'node';
        $bundle = 'res_provider';
        break;

      default:
        return;
    }

    // Get IDs of all content of the specified type on the destination site.
    $query = $this->entityTypeManager->getStorage($entity_type)->getQuery();
    $destination_ids = $query->condition('type', $bundle)->execute();

    // Get IDs of all content in the migration.
    $source_ids = $this->getMigrationSourceIds($migration);
    if (empty($source_ids)) {
      return;
    }

    // Get the mapping of source IDs to destination IDs.
    $id_mapping = $this->getMigrationIdMapping($migration);
    if (empty($id_mapping)) {
      return;
    }

    // Look for any destination IDs that aren't in the source.
    foreach ($destination_ids as $dest_id) {
      if (isset($id_mapping[$dest_id])) {
        $mapped_source_id = $id_mapping[$dest_id];
        if (!in_array($mapped_source_id, $source_ids)) {
          $this->updateDestinationEntity('node', $dest_id, $migration, $options['delete']);
        }
      }
    }
  }

  /**
   * Get IDs of all content in the migration.
   *
   * @param string $migration
   *   The migration ID.
   *
   * @return array
   *   The migration source IDs.
   */
  protected function getMigrationSourceIds($migration) {
    $source_ids = [];

    // Get the migration.
    /** @var \Drupal\migrate\Plugin\MigrationInterface $migration */
    $migration = $this->migrationPluginManager->createInstance($migration);

    // Get the source configuration.
    $source_config = $migration->getSourceConfiguration();
    if (empty($source_config['urls'])) {
      return $source_ids;
    }

    // Get the IDs from each URL.
    foreach ($source_config['urls'] as $url) {
      $data = _uwm_res_import_get_migration_data($url);
      if (!isset($data['data'])) {
        continue;
      }
      foreach ($data['data'] as $item) {
        if (isset($item['attributes']['nid'])) {
          $source_ids[] = $item['attributes']['nid'];
        }
      }
    }

    sort($source_ids, SORT_NUMERIC);

    return array_unique($source_ids);
  }

  /**
   * Get the mapping of source IDs to destination IDs.
   *
   * @param string $migration
   *   The migration ID.
   *
   * @return array
   *   The migration source IDs.
   */
  protected function getMigrationIdMapping($migration) {
    $mapping = [];
    $table = 'migrate_map_' . $migration;

    $select = $this->database->select($table, 'mm');
    $select->fields('mm', ['sourceid1', 'destid1']);
    $rows = $select->execute()->fetchAll(\PDO::FETCH_ASSOC);

    foreach ($rows as $row) {
      if (isset($row['sourceid1']) && isset($row['destid1'])) {
        $source_id = $row['sourceid1'];
        $dest_id = $row['destid1'];
        $mapping[$dest_id] = $source_id;
      }
    }

    return $mapping;
  }

  /**
   * Update content on the destination site.
   *
   * @param string $entity_type
   *   The type of entity to update.
   * @param int $entity_id
   *   The ID of the entity to update.
   * @param string $migration
   *   The migration ID.
   * @param bool $delete
   *   TRUE if the entity should be deleted, instead of unpublished.
   */
  protected function updateDestinationEntity($entity_type, $entity_id, $migration, $delete = FALSE) {
    // Load the entity.
    $storage_handler = $this->entityTypeManager->getStorage($entity_type);
    $entity = $storage_handler->load($entity_id);
    if (isset($entity)) {
      if ($delete) {
        // Delete the entity.
        $entity->delete();
      }
      elseif ($entity->isPublished()) {
        // Unpublish the entity.
        $entity->setUnpublished();
        $entity->save();
      }
    }

    if ($delete) {
      // Remove the migrate map row.
      $table = 'migrate_map_' . $migration;
      $query = $this->database->delete($table)
        ->condition('destid1', $entity_id);
      $query->execute();
    }
  }

  /**
   * One-time import of clinic images.
   *
   * @param string $mappingFile
   *   Json file describing source and destination ids.
   *
   * @command uwm:import-uwmed-clinic-images-to-stevie
   *
   * @usage uwm:import-uwmed-clinic-images-to-stevie
   *   Imports images from @stevie to @uwmed bason on json file mapping.
   */
  public function importUwmedClinicImagesToStevie($mappingFile) {

    // @TODO: Move this to a hook_update_N().
    module_load_include('php', 'uwm_commands',
      'src/Classes/UwmTempImportClinicImage');

    $importer = new UwmTempImportClinicImage();
    $importer::saveClinicImagesLocally($mappingFile);

  }

}
