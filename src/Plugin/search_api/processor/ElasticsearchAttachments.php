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

use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeGuesserInterface;
use Drupal\Core\Config\ConfigFactoryInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\KeyValueStore\KeyValueFactoryInterface;
use Drupal\search_api\Utility\FieldsHelperInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

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

  use PluginFormTrait;

  protected $targetFieldId = 'es_attachment';
  protected $targetFieldType = 'string';

  /**
   * The mime type guesser service.
   *
   * @var \Drupal\Core\File\MimeType\MimeTypeGuesser
   */
  protected $mimeTypeGuesser;

  /**
   * Config factory service.
   *
   * @var \Drupal\Core\Config\ConfigFactoryInterface
   */
  protected $configFactory;

  /**
   * Entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Key value service.
   *
   * @var \Drupal\Core\KeyValueStore\KeyValueFactoryInterface
   */
  protected $keyValue;

  /**
   * Module handler service.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * Search API field helper.
   *
   * @var \Drupal\search_api\Utility\FieldsHelperInterface
   */
  protected $fieldHelper;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration,
                              $plugin_id,
                              array $plugin_definition,
                              MimeTypeGuesserInterface $mime_type_guesser,
                              ConfigFactoryInterface $config_factory,
                              EntityTypeManagerInterface $entity_type_manager,
                              KeyValueFactoryInterface $key_value,
                              ModuleHandlerInterface $module_handler,
                              FieldsHelperInterface $field_helper) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);

    $this->mimeTypeGuesser = $mime_type_guesser;
    $this->configFactory = $config_factory;
    $this->entityTypeManager = $entity_type_manager;
    $this->keyValue = $key_value;
    $this->moduleHandler = $module_handler;
    $this->fieldHelper = $field_helper;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container,
                                array $configuration,
                                $plugin_id,
                                $plugin_definition) {
    return new static($configuration,
                      $plugin_id,
                      $plugin_definition,
                      $container->get('file.mime_type.guesser'),
                      $container->get('config.factory'),
                      $container->get('entity_type.manager'),
                      $container->get('keyvalue'),
                      $container->get('module_handler'),
                      $container->get('search_api.fields_helper')
                    );
  }

  /**
   * {@inheritdoc}
   */
  public function getPropertyDefinitions(DatasourceInterface $datasource = NULL) {
    $properties = [];
    if (!$datasource) {
      $definition = [
        // TODO Come up with better label.
        'label' => $this->t('Search API Elasticsearch attachments'),
        // TODO Come up with better description.
        'description' => $this->t('Search API Elasticsearch attachments.'),
        'type' => $this->targetFieldType,
        'processor_id' => $this->getPluginId(),
        // This will be a hidden field,
        // not something a user can add/remove manually.
        //'hidden' => TRUE,
      ];

      $properties[$this->targetFieldId] = new ProcessorProperty($definition);
    }

    return $properties;
  }

  /**
   * {@inheritdoc}
   */
  public function addFieldValues(ItemInterface $item) {
    ksm($item);
    // TODO We are only working with files for now.
//    if ($item->getDatasource()->getEntityTypeId() == 'file') {
//      $bundle_type = $item->getDatasource()->getItemBundle($item->getOriginalObject());
//      ksm($bundle_type);
//    }

  }

  /**
   * {@inheritdoc}
   */
  public function preIndexSave() {
    // Automatically add field to index if processor is enabled.
    $field = $this->ensureField(NULL, $this->targetFieldId, $this->targetFieldType);

    // Hide the field.
    //$field->setHidden();
  }

  /**
   * {@inheritdoc}
   */
  public function buildConfigurationForm(array $form, FormStateInterface $form_state) {
    if (isset($this->configuration['excluded_extensions'])) {
      $default_excluded_extensions = $this->configuration['excluded_extensions'];
    }
    else {
      $default_excluded_extensions = $this->defaultExcludedExtensions();
    }

    $form['excluded_extensions'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Excluded file extensions'),
      '#default_value' => $default_excluded_extensions,
      '#size' => 80,
      '#maxlength' => 255,
      '#description' => $this->t('File extensions that are excluded from indexing. Separate extensions with a space and do not include the leading dot.<br />Example: "aif art avi bmp gif ico mov oga ogv png psd ra ram rgb flv"<br />Extensions are internally mapped to a MIME type, so it is not necessary to put variations that map to the same type (e.g. tif is sufficient for tif and tiff)'),
    ];
//    $form['number_indexed'] = [
//        '#type' => 'number',
//        '#title' => $this->t('Number of files indexed per file field'),
//        '#default_value' => isset($this->configuration['number_indexed']) ? $this->configuration['number_indexed'] : '0',
//        '#size' => 5,
//        '#min' => 0,
//        '#max' => 99999,
//        '#description' => $this->t('The number of files to index per file field.<br />The order of indexation is the weight in the widget.<br /> 0 for no restriction.'),
//    ];
//    $form['max_filesize'] = [
//        '#type' => 'textfield',
//        '#title' => $this->t('Maximum upload size'),
//        '#default_value' => isset($this->configuration['max_filesize']) ? $this->configuration['max_filesize'] : '0',
//        '#description' => $this->t('Enter a value like "10 KB", "10 MB" or "10 GB" in order to restrict the max file size of files that should be indexed.<br /> Enter "0" for no limit restriction.'),
//        '#size' => 10,
//    ];
    $form['excluded_private'] = [
      '#type' => 'checkbox',
      '#title' => $this->t('Exclude private files'),
      '#default_value' => isset($this->configuration['excluded_private']) ? $this->configuration['excluded_private'] : TRUE,
      '#description' => $this->t('Check this box if you want to exclude private files from being indexed.'),
    ];

    return $form;
  }


  /**
   * Form validation handler.
   *
   * @param array $form
   *   An associative array containing the structure of the plugin form as built
   *   by static::buildConfigurationForm().
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the complete form.
   *
   * @see \Drupal\Core\Plugin\PluginFormInterface::validateConfigurationForm()
   */
  public function validateConfigurationForm(array &$form, FormStateInterface $form_state) {
//    $max_filesize = trim($form_state->getValue('max_filesize'));
//    if ($max_filesize != '0') {
//      $size_info = explode(' ', $max_filesize);
//      if (count($size_info) != 2) {
//        $error = TRUE;
//      }
//      else {
//        $starts_integer = is_int((int) $size_info[0]);
//        $unit_expected = in_array($size_info[1], ['KB', 'MB', 'GB']);
//        $error = !$starts_integer || !$unit_expected;
//      }
//      if ($error) {
//        $form_state->setErrorByName('max_filesize', $this->t('The max filesize option must contain a valid value. You may either enter "0" (for no restriction) or a string like "10 KB, "10 MB" or "10 GB".'));
//      }
//    }
  }

  /**
   * Form submission handler.
   *
   * @param array $form
   *   An associative array containing the structure of the plugin form as built
   *   by static::buildConfigurationForm().
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the complete form.
   *
   * @see \Drupal\Core\Plugin\PluginFormInterface::submitConfigurationForm()
   */
  public function submitConfigurationForm(array &$form, FormStateInterface $form_state) {
    $excluded_extensions = $form_state->getValue('excluded_extensions');
    $excluded_extensions_array = explode(' ', $excluded_extensions);
    $excluded_mimes_array = $this->getExcludedMimes($excluded_extensions_array);
    $excluded_mimes_string = implode(' ', $excluded_mimes_array);

    $this->setConfiguration($form_state->getValues() + ['excluded_mimes' => $excluded_mimes_string]);
  }

  /**
   * Default excluded extensions.
   * see: http://cgit.drupalcode.org/search_api_attachments/tree/src/Plugin/search_api/processor/FilesExtrator.php?h=8.x-1.x#n484
   *
   * @return string
   *   string of file extensions separated by a space.
   */
  public function defaultExcludedExtensions() {
    return 'aif art avi bmp gif ico mov oga ogv png psd ra ram rgb flv';
  }

  /**
   * Get a corresponding array of excluded mime types.
   *
   * Obtained from a space separated string of file extensions.
   * see: http://cgit.drupalcode.org/search_api_attachments/tree/src/Plugin/search_api/processor/FilesExtrator.php?h=8.x-1.x#n501
   *
   * @param string $extensions
   *   If it's not null, the return will correspond to the extensions.
   *   If it is null,the return will correspond to the default excluded
   *   extensions.
   *
   * @return array
   *   Array or mimes.
   */
  public function getExcludedMimes($extensions = NULL) {
    if (!$extensions && isset($this->configuration['excluded_mimes'])) {
      $excluded_mimes_string = $this->configuration['excluded_mimes'];
      $excluded_mimes = explode(' ', $excluded_mimes_string);
    }
    else {
      if (!$extensions) {
        $extensions = explode(' ', $this->defaultExcludedExtensions());
      }
      $excluded_mimes = [];
      foreach ($extensions as $extension) {
        $excluded_mimes[] = $this->mimeTypeGuesser->guess('dummy.' . $extension);
      }
    }
    // Ensure we get an array of unique mime values because many extension can
    // map the the same mime type.
    $excluded_mimes = array_combine($excluded_mimes, $excluded_mimes);
    return array_keys($excluded_mimes);
  }
}