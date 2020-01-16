<?php

namespace Drupal\uwm_bulk_process_content\Form;

use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Defines a form for bulk unplublishing providers.
 */
class UnpublishProvidersForm extends FormBase {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Constructs a new UnpublishProvidersForm object.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('entity_type.manager')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'uwm_bulk_process_content_unpublish_providers_form';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    if (!empty($this->npis)) {
      $form['results'] = [
        '#markup' => '<pre>' . print_r($this->npis, TRUE) . '</pre>',
      ];
    }
    else {
      $form['npis'] = [
        '#title' => $this->t('Paste a list of NPI numbers for providers to unpublish.'),
        '#type' => 'textarea',
        '#description' => $this->t('Submit only NPI numbers separated by a carriage return.'),
        '#rows' => 24,
        '#required' => TRUE,
      ];

      $form['actions'] = ['#type' => 'actions'];
      $form['actions']['submit'] = [
        '#type' => 'submit',
        '#value' => $this->t('Unpublish'),
      ];
    }

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $unpublished = [];
    $already_unpublished = [];
    $missing = [];
    $error = [];

    $npis = preg_split('/\R/', $form_state->getValue('npis'));

    foreach ($npis as $npi) {
      $entities = $this->entityTypeManager
        ->getStorage('node')
        ->loadByProperties(['field_res_npi' => $npi]);

      if ($entities) {
        foreach ($entities as $entity) {
          if ($entity->isPublished()) {
            $entity->setUnpublished();
            if ($entity->save()) {
              $unpublished[$npi] = $entity->label();
            }
            else {
              $error[$npi] = $entity->label();
            }
          }
          else {
            $already_unpublished[$npi] = $entity->label();
          }
        }
      }
      else {
        $missing[$npi] = '';
      }
    }

    if (!empty($unpublished)) {
      $results = [];
      foreach ($unpublished as $npi => $name) {
        $results[] = "$name ($npi)";
      }
      $message = $this->t('The following providers were unpublished: @results', ['@results' => implode(', ', $results)]);
      $this->messenger()->addStatus($message);
    }

    if (!empty($already_unpublished)) {
      $results = [];
      foreach ($already_unpublished as $npi => $name) {
        $results[] = "$name ($npi)";
      }
      $message = $this->t('The following providers were already unpublished: @results', ['@results' => implode(', ', $results)]);
      $this->messenger()->addWarning($message);
    }

    if (!empty($missing)) {
      $results = [];
      foreach ($missing as $npi => $name) {
        $results[] = $npi;
      }
      $message = $this->t('The following NPIs were not found: @results', ['@results' => implode(', ', $results)]);
      $this->messenger()->addWarning($message);
    }

    if (!empty($error)) {
      $results = [];
      foreach ($error as $npi => $name) {
        $results[] = "$name ($npi)";
      }
      $messages['error'] = $this->t('The following providers were not unpublished due to an error: @results', ['@results' => implode(', ', $results)]);
      $this->messenger()->addError($message);
    }
  }

}
