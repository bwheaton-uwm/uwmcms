<?php

namespace Drupal\uwmed_uwmcms_robots_header\EventSubscriber;

use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Event Subscriber RobotsHeader.
 */
class RobotsHeader implements EventSubscriberInterface {

  /**
   * Conditionally set X-Robots-Tag header.
   */
  public function onRespond(FilterResponseEvent $event) {
    /*
     * Check the request base-url, if it isn't www.uwmedicine.org,
     * add a X-Robots-Tag: noindex,nofollow header to the response.
     */
    $request_host = $event->getRequest()->getHost();
    if ($request_host !== 'www.uwmedicine.org') {
      $response = $event->getResponse();
      $response->headers->set('X-Robots-Tag', 'noindex,nofollow');
    }
  }

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    // Register our custom function with the RESPONSE events.
    $events[KernelEvents::RESPONSE][] = ['onRespond'];
    return $events;
  }

}
