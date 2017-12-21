<?php

namespace Drupal\search_api_elasticsearch_attachments\EventSubscriber;

use Drupal\elasticsearch_connector\Event\PrepareIndexMappingEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * {@inheritdoc}
 */
class PrepareIndexMapping implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[PrepareIndexMappingEvent::PREPARE_INDEX_MAPPING] = 'indexMapping';
    return $events;
  }

  /**
   * Method to prepare index mapping.
   *
   * @param \Drupal\elasticsearch_connector\Event\PrepareIndexMappingEvent $event
   *   The PrepareIndexMappingEvent event.
   */
  public function indexMapping(PrepareIndexMappingEvent $event) {
    $indexMappingParams = $event->getIndexMappingParams();
    // Exclude our source field from getting saved in ES.
    // See: https://qbox.io/blog/index-attachments-files-elasticsearch-mapper
    $indexMappingParams['body'][$indexMappingParams['type']]['_source']['excludes'][] = 'es_attachment';
    $event->setIndexMappingParams($indexMappingParams);
  }

}
