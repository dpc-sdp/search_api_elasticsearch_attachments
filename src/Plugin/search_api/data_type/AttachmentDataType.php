<?php

namespace Drupal\search_api_elasticsearch_attachments\Plugin\search_api\data_type;

use Drupal\search_api\DataType\DataTypePluginBase;

/**
 * Provides a attachment data type.
 *
 * @SearchApiDataType(
 *   id = "attachment",
 *   label = @Translation("Attachment"),
 *   description = @Translation("Attachment fields are used for indexing files into Elasticsearch."),
 *   default = "true"
 * )
 */
class AttachmentDataType extends DataTypePluginBase {

}
