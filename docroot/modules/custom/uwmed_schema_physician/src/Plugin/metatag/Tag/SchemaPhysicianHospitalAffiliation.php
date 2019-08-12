<?php

namespace Drupal\uwmed_schema_physician\Plugin\metatag\Tag;

use Drupal\schema_metatag\SchemaMetatagManager;
use Drupal\schema_metatag\Plugin\metatag\Tag\SchemaAddressTrait;
use Drupal\schema_metatag\Plugin\metatag\Tag\SchemaPersonOrgBase;
use Drupal\image\Entity\ImageStyle;
use Drupal\Component\Utility\UrlHelper;

/**
 * Provides a plugin for the 'schema_physician_name' meta tag.
 *
 * - 'id' should be a globally unique id.
 * - 'name' should match the Schema.org element name.
 * - 'group' should match the id of the group that defines the Schema.org type.
 *
 * @MetatagTag(
 *   id = "schema_physician_hospital_affiliation",
 *   label = @Translation("Hospital Affiliation"),
 *   description = @Translation("A hospital with which the physician or office is affiliated."),
 *   name = "hospitalAffiliation",
 *   group = "schema_physician",
 *   weight = 5,
 *   type = "string",
 *   secure = FALSE,
 *   multiple = FALSE
 * )
 */
class SchemaPhysicianHospitalAffiliation extends SchemaPersonOrgBase {

  use SchemaAddressTrait;

  /**
   * {@inheritdoc}
   */
  public static function personOrgFormKeys() {
    $new_fields = [
      'image',
      'address',
      'telephone',
    ];

    $fields = parent::personOrgFormKeys();

    $fields = array_merge($fields, $new_fields);

    return $fields;
  }

  /**
   * {@inheritdoc}
   */
  public function personOrgForm($input_values) {
    $form = parent::personOrgForm($input_values);

    $input_values += SchemaMetatagManager::defaultInputValues();
    $value = $input_values['value'];

    // Get the id for the nested @type element.
    $visibility_selector = $input_values['visibility_selector'];
    $selector = ':input[name="' . $input_values['visibility_selector'] . '[@type]"]';
    $visibility = ['invisible' => [$selector => ['value' => '']]];
    $selector2 = SchemaMetatagManager::altSelector($selector);
    $visibility2 = ['invisible' => [$selector2 => ['value' => '']]];
    $visibility['invisible'] = [$visibility['invisible'], $visibility2['invisible']];

    $form['telephone'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Telephone'),
      '#default_value' => !empty($value['telephone']) ? $value['telephone'] : '',
      '#maxlength' => 255,
      '#required' => $input_values['#required'],
      '#description' => $this->t("The hospital telephone number."),
    ];

    $image_values = [
      'title' => $this->t('image'),
      'description' => $this->t("An image of the item."),
      'value' => !empty($value['image']) ? $value['image'] : [],
      '#required' => $input_values['#required'],
      'visibility_selector' => $visibility_selector . '[image]',
    ];
    $form['image'] = static::imageForm($image_values);

    $address_values = [
      'title' => $this->t('Address'),
      'description' => $this->t('The address of the place.'),
      'value' => !empty($value['address']) ? $value['address'] : [],
      '#required' => $input_values['#required'],
      'visibility_selector' => $visibility_selector . '[address]',
    ];

    $form['address'] = $this->postalAddressForm($address_values);
    $form['address']['#states'] = $visibility;

    $keys = static::personOrgFormKeys();
    foreach ($keys as $key) {
      if ($key != '@type') {
        $form[$key]['#states'] = $visibility;
      }
    }

    return $form;
  }

  /**
   * Generate a form element for this meta tag.
   */
  public function form(array $element = []) {
    $form = parent::form($element);

    $form['@type']['#options'] = [
      'Hospital' => $this->t('Hospital'),
    ];

    $form['name']['#attributes']['placeholder'] = '[site:name]';
    $form['url']['#attributes']['placeholder'] = '[site:url]';

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public static function outputValue($input_value) {
    $value = $input_value;

    // Add the clinic's image.
    if (isset($input_value['image']['url'])) {
      $nid = $input_value['image']['url'];
      $entity = \Drupal::entityTypeManager()->getStorage('node')->load($nid);
      $image_style = ImageStyle::load('clinic_page_576_288');

      if ($entity->hasField('field_res_image') && $file = $entity->get('field_res_image')->entity) {
        $url = $image_style->buildUrl($file->getFileUri());

        // Remove the query string from the URL.
        $url_parts = UrlHelper::parse($url);
        $url = $url_parts['path'];
      }

      // If the clinic has no image, use the default image.
      if (!isset($url)) {
        $host = \Drupal::request()->getSchemeAndHttpHost();
        $theme_path = \Drupal::theme()->getActiveTheme()->getPath();
        $default_image = 'src/assets/uwmed_location_card_placeholder.svg';
        $url = "$host/$theme_path/$default_image";
      }

      $value['image'] = $url;
    }

    return $value;
  }

}
