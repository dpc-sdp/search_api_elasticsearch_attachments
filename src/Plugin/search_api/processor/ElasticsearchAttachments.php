<?php

namespace Drupal\search_api_elasticsearch_attachments\Plugin\search_api\processor;

use Drupal\search_api\Datasource\DatasourceInterface;
use Drupal\search_api\Item\ItemInterface;
use Drupal\search_api\Processor\ProcessorPluginBase;
use Drupal\search_api\Processor\ProcessorProperty;
use Drupal\Core\Plugin\PluginFormInterface;
use Drupal\Core\Form\FormStateInterface;
use Drupal\search_api\IndexInterface;
use Drupal\search_api\Plugin\PluginFormTrait;
use Drupal\Component\Utility\Html;
use Drupal\node\NodeInterface;
use Drupal\Core\TypedData\ComplexDataInterface;

/**
 * Provides file fields processor for Elasticsearch Attachments.
 *
 * @SearchApiProcessor(
 *   id = "elasticsearch_attachments",
 *   label = @Translation("Elasticsearch attachments"),
 *   description = @Translation("Adds the Elasticsearch attachments content to the indexed data."),
 *   stages = {
 *     "add_properties" = 0,
 *   },
 *   locked = false,
 *   hidden = false,
 * )
 */

class ElasticsearchAttachments extends ProcessorPluginBase implements PluginFormInterface {

}