<?php

namespace Drupal\uwm_import_epic\Theme;

use Drupal\user\Theme\AdminNegotiator;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Node\NodeInterface;

/**
 * Use the admin theme on additional desired pages.
 *
 * Note: inherit determineActiveTheme() from AdminNegotiator which finds the
 * admin theme from config.
 *
 * TODO: could move this to a more general module if cases expand.
 */
class UwmAdminThemeNegotiator extends AdminNegotiator {

  /**
   * {@inheritdoc}
   */
  public function applies(RouteMatchInterface $route_match) {

    // Return true if viewing a Filebrowser Directory Listing node.
    if ($route_match->getRouteName() === 'entity.node.canonical') {

      $node = $route_match->getParameter('node');

      if (!empty($node) && $node instanceof NodeInterface && $node->bundle() === 'dir_listing') {

        return TRUE;

      }

    }

    return FALSE;

  }

}
