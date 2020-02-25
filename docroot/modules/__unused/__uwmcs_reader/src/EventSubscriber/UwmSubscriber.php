<?php

namespace Drupal\uwmcs_reader\EventSubscriber;

use Drupal\uwmcs_reader\Controller\UwmCreator;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class UwmSubscriber.
 *
 * @package Drupal\uwmcs_reader\EventSubscriber
 */
class UwmSubscriber implements EventSubscriberInterface {

  /**
   * Create missing node for IM API data, if missing.
   *
   * @param \Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent $event
   *   Field value.
   */
  public function createMissingNodes(GetResponseForExceptionEvent $event) {

    $uwm = new UwmCreator();
    $uwm->createMissingApiNode(TRUE);

  }

  /**
   * Redirect 403 errors for unpublished content to front.
   *
   * @param \Symfony\Component\HttpKernel\Event\FilterResponseEvent $event
   *   Field value.
   */
  public function redirectAccessDenied(FilterResponseEvent $event) {

    if ($event->getResponse()->getStatusCode() == 403) {

      // Editors rightfully do not get 403, so we don't check permissions.
      drupal_set_message('We could not find a requested page or page element.', 'warning');

      $response = new RedirectResponse('/');
      $event->setResponse($response);

    }

  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {

    $events[KernelEvents::EXCEPTION][] = ['createMissingNodes'];
    $events[KernelEvents::RESPONSE][] = ['redirectAccessDenied'];
    return $events;

  }

}
