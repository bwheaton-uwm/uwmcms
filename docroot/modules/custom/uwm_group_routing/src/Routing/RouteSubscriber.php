<?php

namespace Drupal\uwm_group_routing\Routing;

use Drupal\Core\Routing\RouteSubscriberBase;
use Symfony\Component\Routing\RouteCollection;

/**
 * Unifies the Group Module so all tabs use the admin theme
 *
 * Class RouteSubscriber.
 *
 * @package Drupal\uwm_group_routing\Routing
 */

 class RouteSubscriber extends RouteSubscriberBase {


  /**
   * {@inheritdoc}
   */
  public function alterRoutes(RouteCollection $collection) {
    $current_uri = \Drupal::request()->getRequestUri();
    print_r('hello', $current_uri);
    if ($route = $collection->get('view.group_members.page_1')) {
      $route->addOptions(['_admin_route' => TRUE]);
    }
    if ($route =  $collection->get('view.group_nodes.page_1')) {
      $route->addOptions(['_admin_route' => TRUE]);
    }
    if ($route =  $collection->get('page.group')) {
      $route->addOptions(['_admin_route' => TRUE]);
    }
  }


 }
