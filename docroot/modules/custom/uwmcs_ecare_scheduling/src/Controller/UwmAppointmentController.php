<?php

namespace Drupal\uwmcs_ecare_scheduling\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Ajax\AjaxResponse;
use Drupal\Core\Ajax\HtmlCommand;
use Drupal\node\NodeInterface;

/**
 * Controller for Epic appointment-scheduling integrations.
 */
class UwmAppointmentController extends ControllerBase {

  /**
   * Callback for '/uwm-appointment/provider/{node}/{nojs}' route.
   *
   * This expects an AJAX request and loads the appointment flow for the given
   * provider.
   *
   * @param \Drupal\node\NodeInterface $node
   *   The upcasted node object; should be a res_provider node.
   * @param string $nojs
   *   Either 'ajax' or 'nojs', to indicate the type of request.
   */
  public function appointmentProviderFlow(NodeInterface $node, $nojs) {

    if ($node->bundle() !== 'res_provider') {
      // TODO: error-handle this?
    }

    if ($nojs === 'ajax') {

      $response = new AjaxResponse();

      // TODO: ensure theme hook exists, defined by uwmbase theme.
      $build = [
        '#theme' => 'uwm_appointment_provider_flow',
        '#node' => $node,
      ];

      $response->addCommand(new HtmlCommand('#appointment-provider-flow', $build));

      return $response;

    }
    else {

    }

  }

}
