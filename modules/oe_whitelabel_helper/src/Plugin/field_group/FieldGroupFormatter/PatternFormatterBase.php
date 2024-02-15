<?php

declare(strict_types=1);

namespace Drupal\oe_whitelabel_helper\Plugin\field_group\FieldGroupFormatter;

use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Render\Element;
use Drupal\field_group\FieldGroupFormatterBase;
use Drupal\ui_patterns\UiPatternsManager;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Base class for field group formatters that use a pattern for rendering.
 *
 * Field group formatters extending this class generate a pattern context that
 * is compatible with the one generated by the ui_patterns_field_group module.
 *
 * If you need to override pattern templates based on node, bundle or view mode
 * just enable the ui_patterns_field_group module.
 *
 * @see https://ui-patterns.readthedocs.io/en/8.x-1.x/content/developer-documentation.html#working-with-pattern-suggestions
 */
abstract class PatternFormatterBase extends FieldGroupFormatterBase implements ContainerFactoryPluginInterface {

  /**
   * UI Patterns manager.
   *
   * @var \Drupal\ui_patterns\UiPatternsManager
   */
  protected $patternsManager;

  /**
   * PatternFormatterBase constructor.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param array $plugin_definition
   *   The plugin implementation definition.
   * @param \Drupal\ui_patterns\UiPatternsManager $patterns_manager
   *   UI Patterns manager.
   */
  public function __construct(array $configuration, string $plugin_id, array $plugin_definition, UiPatternsManager $patterns_manager) {
    parent::__construct($plugin_id, $plugin_definition, $configuration['group'], $configuration['settings'], $configuration['label']);
    $this->configuration = $configuration;
    $this->patternsManager = $patterns_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('plugin.manager.ui_patterns')
    );
  }

  /**
   * {@inheritdoc}
   */
  public static function defaultSettings() {
    return [
      'label' => '',
      'variant' => '',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function settingsForm() {
    $pattern = $this->patternsManager->getDefinition($this->getPatternId());

    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Field group label'),
      '#default_value' => $this->label,
    ];

    if ($pattern->hasVariants()) {
      $form['variant'] = [
        '#title' => $this->t('Variant'),
        '#type' => 'select',
        '#options' => $pattern->getVariantsAsOptions(),
        '#default_value' => $this->getSetting('variant'),
      ];
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function settingsSummary() {
    $summary = [];

    if ($this->getLabel()) {
      $summary[] = $this->t('Label: @label', ['@label' => $this->getLabel()]);
    }

    if ($this->getSetting('variant')) {
      $summary[] = $this->t('Variant: @variant', ['@variant' => $this->getSetting('variant')]);
    }

    return $summary;
  }

  /**
   * {@inheritdoc}
   */
  public function preRender(&$element, $rendering_object) {
    parent::preRender($element, $rendering_object);

    $fields = $this->getFields($element, $rendering_object);
    if ($fields === NULL) {
      // Don't render the pattern.
      return;
    }
    // Instantiate the pattern render array.
    $pattern = [
      '#type' => 'pattern',
      '#id' => $this->getPatternId(),
      '#variant' => $this->getSetting('variant'),
      '#fields' => $fields,
      '#context' => [
        'type' => 'field_group',
        'group_name' => $element['#group_name'],
        'entity_type' => $element['#entity_type'],
        'bundle' => $element['#bundle'],
        'view_mode' => $this->group->mode,
      ],
    ];

    // Remove all renderable elements, while keeping render metadata as that can
    // be used to further manipulate the render array.
    foreach (Element::children($element) as $key) {
      unset($element[$key]);
    }
    $element += [
      'pattern' => $pattern,
    ];
  }

  /**
   * Return pattern ID for the current formatter plugin.
   *
   * @return string
   *   Pattern ID.
   */
  abstract protected function getPatternId(): string;

  /**
   * Return list of fields for the current pattern.
   *
   * @param array $element
   *   Field group render element.
   * @param object $rendering_object
   *   Field group rendering object.
   *
   * @return array|null
   *   Pattern fields to be rendered, or NULL if the field group
   *   should not be displayed at all.
   */
  abstract protected function getFields(array &$element, $rendering_object): ?array;

}
