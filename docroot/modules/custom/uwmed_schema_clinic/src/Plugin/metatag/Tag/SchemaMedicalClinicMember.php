<?php

namespace Drupal\uwmed_schema_clinic\Plugin\metatag\Tag;

use Drupal\schema_metatag\SchemaMetatagManager;
use Drupal\schema_metatag\Plugin\metatag\Tag\SchemaNameBase;
use Drupal\schema_metatag\Plugin\metatag\Tag\SchemaPivotTrait;

/**
 * Provides a plugin for the 'schema_clinic_member' meta tag.
 *
 * - 'id' should be a globally unique id.
 * - 'name' should match the Schema.org element name.
 * - 'group' should match the id of the group that defines the Schema.org type.
 *
 * Note: this depends on two patches to prevent values containing commas from
 * being exploded to multiple values:
 * https://www.drupal.org/project/metatag/issues/3067803
 * https://www.drupal.org/project/schema_metatag/issues/2976935
 *
 * @MetatagTag(
 *   id = "schema_clinic_member",
 *   label = @Translation("Members"),
 *   description = @Translation("Providers who work at this clinic."),
 *   name = "member",
 *   group = "schema_medical_clinic",
 *   weight = 10,
 *   type = "string",
 *   secure = FALSE,
 *   multiple = TRUE
 * )
 */
class SchemaMedicalClinicMember extends SchemaNameBase {

  use SchemaPivotTrait;

  /**
   * Define member form keys.
   *
   * @return array
   *   The form keys.
   */
  public static function memberFormKeys() {
    return [
      '@type',
      'pivot',
      'name',
    ];
  }

  /**
   * Define the member form.
   *
   * Note: skip the potential URL property for Person objects - token for an
   * entity ref field value does not extract the multiple values for a property
   * of each referenced entity.
   *
   * @param array $input_values
   *   The input values.
   *
   * @return array
   *   The form.
   */
  public function memberForm(array $input_values) {
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

    // Add a pivot option to the form.
    $form['pivot'] = $this->pivotForm($value);
    $form['pivot']['#states'] = $visibility;

    $form['@type'] = [
      '#type' => 'select',
      '#title' => $this->t('@type'),
      '#default_value' => !empty($value['@type']) ? $value['@type'] : '',
      '#empty_option' => t('- None -'),
      '#empty_value' => '',
      '#options' => [
        'Person' => $this->t('Person'),
      ],
      '#required' => $input_values['#required'],
      '#weight' => -10,
    ];

    $form['name'] = [
      '#type' => 'textfield',
      '#title' => $this->t('name'),
      '#default_value' => !empty($value['name']) ? $value['name'] : '',
      '#maxlength' => 255,
      '#required' => $input_values['#required'],
      '#description' => $this->t("The name of the person."),
    ];

    $keys = static::memberFormKeys();
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

    $form = $this->memberForm($input_values);

    if (empty($this->multiple())) {
      unset($form['pivot']);
    }

    return $form;
  }

}
