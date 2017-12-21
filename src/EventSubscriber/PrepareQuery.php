<?php

namespace Drupal\search_api_elasticsearch_attachments\EventSubscriber;

use Drupal\elasticsearch_connector\Event\PrepareSearchQueryEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * {@inheritdoc}
 */
class PrepareQuery implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[PrepareSearchQueryEvent::PREPARE_QUERY] = 'query';
    return $events;
  }

  /**
   * Method to prepare query.
   *
   * @param \Drupal\elasticsearch_connector\Event\PrepareSearchQueryEvent $event
   *   The PrepareSearchQueryEvent event.
   */
  public function query(PrepareSearchQueryEvent $event) {
    $elasticSearchQuery = $event->getElasticSearchQuery();
    $elasticSearchQuery['query_search_string']['query_string']['fields'][] = 'es_attachment.content';
    $event->setElasticSearchQuery($elasticSearchQuery);
  }

}
