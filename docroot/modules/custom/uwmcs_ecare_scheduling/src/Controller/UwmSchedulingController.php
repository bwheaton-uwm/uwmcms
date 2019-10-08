<?php

namespace Drupal\uwmcs_ecare_scheduling\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\node\NodeInterface;

/**
 * Controller for Epic scheduling integrations.
 */
class UwmSchedulingController extends ControllerBase {

  /**
   * Callback for '/uwm-schedule/provider/{node}/{nojs}' route.
   *
   * This expects an AJAX request and loads the scheduling flow for the given
   * provider.
   *
   * @param \Drupal\node\NodeInterface $node
   *   The upcasted node object; should be a res_provider node.
   * @param string $nojs
   *   Either 'ajax' or 'nojs', to indicate the type of request.
   */
  public function providerSchedulingFlow(NodeInterface $node, $nojs) {

    if ($node->bundle() !== 'res_provider') {
      // TODO: error-handle this?
    }

    if ($nojs === 'ajax') {

      $response = new AjaxResponse();

      $build = [
        '#markup' => "<p>AJAX response</p>",
      ];

      $response->addCommand(new HtmlCommand('#scheduling-flow', $build));

      return $response;

    }
    else {

    }

  }

}
