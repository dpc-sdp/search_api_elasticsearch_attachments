<?php

namespace Drupal\search_api_elasticsearch_attachments\EventSubscriber;

use Drupal\elasticsearch_connector\Event\BuildSearchParamsEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * {@inheritdoc}
 */
class BuildSearchParams implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[BuildSearchParamsEvent::BUILD_QUERY] = 'buildSearchParams';
    return $events;
  }

  /**
   * Method to build Params.
   *
   * @param \Drupal\elasticsearch_connector\Event\BuildSearchParamsEvent $event
   *   The BuildSearchParamsEvent event.
   */
  public function buildSearchParams(BuildSearchParamsEvent $event) {
    $params = $event->getElasticSearchParams();
    // See: https://github.com/elastic/elasticsearch-php/issues/394
    $params['body']['highlight']['fields']['es_attachment.content'] = (object)[];
    $event->setElasticSearchParams($params);
  }

}
