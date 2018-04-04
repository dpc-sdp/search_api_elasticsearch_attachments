<?php

namespace Drupal\search_api_elasticsearch_attachments\EventSubscriber;

use Drupal\elasticsearch_connector\Event\BuildSearchParamsEvent;
use Drupal\search_api\Entity\Index;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\search_api_elasticsearch_attachments\Helpers;

/**
 * {@inheritdoc}
 */
class BuildSearchParams implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[BuildSearchParamsEvent::BUILD_QUERY] = 'searchParams';
    return $events;
  }

  /**
   * Method to build Params.
   *
   * @param \Drupal\elasticsearch_connector\Event\BuildSearchParamsEvent $event
   *   The BuildSearchParamsEvent event.
   */
  public function searchParams(BuildSearchParamsEvent $event) {
    $params = $event->getElasticSearchParams();

    // Default Prefix and Suffix.
    $prefix = '<strong>';
    $suffix = '</strong>';

    // We need to get the Prefix and Suffix from processor.
    $indexName = Helpers::getIndexName($event->getIndexName());
    $processors = Index::load($indexName)->getProcessors();

    if (!empty($processors['elasticsearch_attachments_highlight'])) {
      $processorConf = $processors['elasticsearch_attachments_highlight']->getConfiguration();
      $prefix = $processorConf['prefix'];
      $suffix = $processorConf['suffix'];
    }

    // See: https://github.com/elastic/elasticsearch-php/issues/394
    $params['body']['highlight']['fields']['es_attachment.content'] = (object) [];
    $params['body']['highlight']['pre_tags'] = [$prefix];
    $params['body']['highlight']['post_tags'] = [$suffix];

    $event->setElasticSearchParams($params);
  }

}
