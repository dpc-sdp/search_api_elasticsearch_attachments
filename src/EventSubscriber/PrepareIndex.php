<?php

namespace Drupal\search_api_elasticsearch_attachments\EventSubscriber;

//use Drupal\elasticsearch_connector\ElasticSearch\Parameters\Factory\IndexFactory;
use Drupal\elasticsearch_connector\Event\PrepareIndexEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Drupal\search_api\Entity\Index;


/**
 * {@inheritdoc}
 */
class PrepareIndex implements EventSubscriberInterface {

  /**
   * {@inheritdoc}
   */
  public static function getSubscribedEvents() {
    $events[PrepareIndexEvent::PREPARE_INDEX] = 'prepareIndex';
    return $events;
  }

  /**
   * Method to prepare index.
   *
   * @param Drupal\elasticsearch_connector\Event\PrepareIndexEvent $event
   *   The PrepareIndexEvent event.
   */
  public function prepareIndex(PrepareIndexEvent $event) {
//    $indexConfig = $event->getIndexConfig();
////ksm($indexConfig);
//    // See IndexFactory:getIndexName()
//    $options = \Drupal::database()->getConnectionOptions();
//    $site_database = $options['database'];
//    $indexName = str_replace('elasticsearch_index_' . $site_database . '_', '', $event->getIndexName());
//    //ksm(Index::load($indexName));
//
//    $indexConfig['body'][$indexName]['properties']['es_attachment']['fields']['content']['store'] = true;
//    $event->setIndexConfig($indexConfig);
  }

}