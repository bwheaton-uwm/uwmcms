<?php

namespace Drupal\uwmed_schema_clinic\Plugin\metatag\Tag;

use Drupal\schema_metatag\SchemaMetatagManager;
use Drupal\schema_metatag\Plugin\metatag\Tag\SchemaNameBase;

/**
 * Provides a plugin for the 'schema_clinic_potential_action' meta tag.
 *
 * - 'id' should be a globally unique id.
 * - 'name' should match the Schema.org element name.
 * - 'group' should match the id of the group that defines the Schema.org type.
 *
 * @MetatagTag(
 *   id = "schema_clinic_potential_action",
 *   label = @Translation("Potential Action"),
 *   description = @Translation("The appointment action to which this clinic points users."),
 *   name = "potentialAction",
 *   group = "schema_medical_clinic",
 *   weight = 13,
 *   type = "string",
 *   secure = FALSE,
 *   multiple = FALSE
 * )
 */
class SchemaMedicalClinicPotentialAction extends SchemaNameBase {

  /**
   * Define potential action form keys.
   *
   * @return array
   *   The form keys.
   */
  public static function potentialActionFormKeys() {
    return [
      '@type',
      'target',
    ];
  }

  /**
   * Define the potential action form.
   *
   * @param array $input_values
   *   The input values.
   *
   * @return array
   *   The form.
   */
  public function potentialActionForm(array $input_values) {
    $input_values += SchemaMetatagManager::defaultInputValues();
    $value = $input_values['value'];

    // Get the id for the nested @type element.
    $visibility_selector = $input_values['visibility_selector'];
    $selector = ':input[name="' . $visibility_selector . '[@type]"]';
    $visibility = ['invisible' => [$selector => ['value' => '']]];
    $selector2 = SchemaMetatagManager::altSelector($selector);
    $visibility2 = ['invisible' => [$selector2 => ['value' => '']]];
    $visibility['invisible'] = [$visibility['invisible'], $visibility2['invisible']];

    $form['#type'] = 'fieldset';
    $form['#title'] = $input_values['title'];
    $form['#description'] = $input_values['description'];
    $form['#tree'] = TRUE;
    $form['#multiple'] = TRUE;

    $form['@type'] = [
      '#type' => 'select',
      '#title' => $this->t('@type'),
      '#default_value' => !empty($value['@type']) ? $value['@type'] : '',
      '#empty_option' => t('- None -'),
      '#empty_value' => '',
      '#options' => [
        'ScheduleAction' => $this->t('ScheduleAction'),
      ],
      '#required' => $input_values['#required'],
      '#weight' => -10,
    ];

    $form['target'] = [
      '#type' => 'textfield',
      '#title' => $this->t('target'),
      '#default_value' => !empty($value['target']) ? $value['target'] : '',
      '#maxlength' => 255,
      '#required' => $input_values['#required'],
      '#description' => $this->t("The URL to access to perform this action."),
    ];

    $keys = static::potentialActionFormKeys();
    foreach ($keys as $key) {
      if ($key != '@type') {
        $form[$key]['#states'] = $visibility;
      }
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function form(array $element = []) {
    $value = SchemaMetatagManager::unserialize($this->value());

    $input_values = [
      'title' => $this->label(),
      'description' => $this->description(),
      'value' => $value,
      '#required' => isset($element['#required']) ? $element['#required'] : FALSE,
      'visibility_selector' => $this->visibilitySelector(),
    ];

    $form = $this->potentialActionForm($input_values);

    return $form;
  }

  /**
   * {@inheritdoc}
   *
   * The `[site:url]` token will generate the 'stevie' domain; replace with
   * 'www' on prod.
   */
  public static function outputValue($input_value) {

    if (is_array($input_value) && !empty($input_value['target']) && is_string($input_value['target'])) {

      $input_value['target'] = str_replace('stevie.cms.', 'www.', $input_value['target']);

    }

    return $input_value;

  }

}
