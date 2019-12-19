<?php

namespace Drupal\uwmcs_utils\EventSubscriber;

/**
 * Define UwmcsUtilsEventSubscriber class.
 *
 * A simple utility event subscriber class. Currently we add an
 * additional subscribed event for the framework. This allows setting
 * custom HTTP headers in the onRespond event and, telling robots not
 * to crawl pages on pre-production.
 */

use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * Class UwmcsUtilsEventSubscriber.
 */
class UwmcsUtilsEventSubscriber implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   *
   * @return array
   *   Subscribed kernel events.
   */
  public static function getSubscribedEvents() {

    return [
      KernelEvents::RESPONSE => 'onRespond',
    ];

  }

  /**
   * {@inheritdoc}
   */
  public function onRespond(FilterResponseEvent $event) {

    /***
     * Set no-index headers, so search engines do not crawl
     * pre-produciton. According to Google, we have to make sure there's
     * no block statement in docroot/robots.txt, so that the page header
     * is honored.
     *
     * @see https://developers.google.com/search/reference/robots_meta_tag
     * @see https://support.google.com/webmasters/answer/93710?hl=en
     */

    if (empty($_ENV['AH_SITE_ENVIRONMENT']) || $_ENV['AH_SITE_ENVIRONMENT'] !== 'prod') {

      $response = $event->getResponse();
      $response->headers->set('X-Robots-Tag', 'noindex, nofollow');

    }

  }

}
