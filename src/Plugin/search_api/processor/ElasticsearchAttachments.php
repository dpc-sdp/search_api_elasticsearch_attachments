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
 *     "pre_index_save" = 0,
 *     "preprocess_index" = -20,
 *   },
 *   locked = false,
 *   hidden = false,
 * )
 */

class ElasticsearchAttachments extends ProcessorPluginBase implements PluginFormInterface {

  protected $targetFieldPrefix = 'es_attachment';

  /**
   * {@inheritdoc}
   */
  public function getPropertyDefinitions(DatasourceInterface $datasource = NULL) {

  }

  /**
   * {@inheritdoc}
   */
  public function addFieldValues(ItemInterface $item) {
    // TODO We are only working with files for now.
    if ($item->getDatasource()->getEntityTypeId() == 'file') {
      $bundle_type = $item->getDatasource()->getItemBundle($item->getOriginalObject());
    }

  }

  /**
   * {@inheritdoc}
   */
  public function preIndexSave() {
    // Automatically add field to index if processor is enabled.
    $field = $this->ensureField(NULL, $this->targetFieldId, 'string');

    // Hide the field.
    $field->setHidden();
  }

}