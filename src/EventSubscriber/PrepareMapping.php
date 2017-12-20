<?php

namespace Drupal\search_api_elasticsearch_attachments\EventSubscriber;

use Drupal\elasticsearch_connector\Event\PrepareMappingEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * {@inheritdoc}
 */
class PrepareMapping implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[PrepareMappingEvent::PREPARE_MAPPING] = 'prepareMapping';
    return $events;
  }

  /**
   * Method to prepare query.
   *
   * @param \Drupal\elasticsearch_connector\Event\PrepareMappingEvent $event
   *   The PrepareMappingEvent event.
   */
  public function prepareMapping(PrepareMappingEvent $event) {
    $mappingConfig = $event->getMappingConfig();
    $type = $event->getMappingType();
    if ($type == 'attachment') {
      $mappingConfig['fields'] = [
        "content" => [
          "store" => TRUE,
          "term_vector" => "with_positions_offsets",
        ],
      ];
      $event->setMappingConfig($mappingConfig);
    }
  }

}
