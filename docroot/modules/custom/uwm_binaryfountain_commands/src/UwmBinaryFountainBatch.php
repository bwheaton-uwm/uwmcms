<?php

namespace Drupal\uwm_binaryfountain_commands;

/**
 * Class BatchService.
 */
class UwmBinaryFountainBatch {

  /**
   * Batch process callback.
   *
   * @param int $id
   *   Id of the batch.
   * @param string $operation_details
   *   Details of the operation.
   * @param object $context
   *   Context for operations.
   */
  public function processMyNode($id, $operation_details, &$context) {

    // Simulate long process by waiting 100 microseconds.
    usleep(100);

    // Store some results for post-processing in the 'finished' callback.
    // The contents of 'results' will be available as $results in the
    // 'finished' function (in this example, batch_example_finished()).
    $context['results'][] = $id;

    // Optional message displayed under the progressbar.
    $context['message'] = t('Running Batch "@id" @details',
      ['@id' => $id, '@details' => $operation_details]
    );

  }

  /**
   * Batch Finished callback.
   *
   * @param bool $success
   *   Success of the operation.
   * @param array $results
   *   Array of results for post processing.
   * @param array $operations
   *   Array of operations.
   */
  public function processMyNodeFinished($success, array $results, array $operations) {
    $messenger = \Drupal::messenger();
    if ($success) {
      // Here we could do something meaningful with the results.
      // We just display the number of nodes we processed...
      $messenger->addMessage(t('@count results processed.', ['@count' => count($results)]));
    }
    else {
      // An error occurred.
      // $operations contains the operations that remained unprocessed.
      $error_operation = reset($operations);
      $messenger->addMessage(
        t('An error occurred while processing @operation with arguments : @args',
          [
            '@operation' => $error_operation[0],
            '@args' => print_r($error_operation[0], TRUE),
          ]
        )
      );
    }
  }

  /**
   * Sends a collection of node ids to batching and the refresh operation.
   *
   * @param array $nids
   *   The node ids to refresh.
   */
  public function batchOperation(array $nids = []) {

    $this->loggerChannelFactory->get(__CLASS__)->info('Update nodes batch operations start');

    $operations = [];
    $numOperations = 0;
    $batchId = 1;

    if (!empty($nids)) {
      foreach ($nids as $nid) {

        $this->output()->writeln("Preparing batch: " . $batchId);

        $operations[] = [
          '\Drupal\uwm_binaryfountain_commands\UwmBinaryFountainBatch::processMyNode',
          [
            $batchId,
            t('Updating node @nid', ['@nid' => $nid]),
          ],
        ];
        $batchId++;
        $numOperations++;

      }

    }
    else {
      $this->logger()->warning('No nodes of this type @type', ['@type' => $type]);
    }

    $batch = [
      'title' => t('Updating @num node(s)', ['@num' => $numOperations]),
      'operations' => $operations,
      'finished' => '\Drupal\uwm_binaryfountain_commands\UwmBinaryFountainBatch::processMyNodeFinished',
    ];

    batch_set($batch);
    drush_backend_batch_process();

    $this->logger()->notice(__CLASS__ . 'Batch operations end.');
    $this->loggerChannelFactory->get(__CLASS__)->info('Update batch operations end.');

  }

}
