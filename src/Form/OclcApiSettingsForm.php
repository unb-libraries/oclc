<?php

namespace Drupal\oclc_api\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Settings form for oclc_api module.
 *
 * @package Drupal\oclc_api\Form
 */
class OclcApiSettingsForm extends ConfigFormBase {

  /**
   * {@inheritDoc}
   */
  protected function getEditableConfigNames() {
    return [
      'default' => 'oclc_api.settings',
    ];
  }

  /**
   * {@inheritDoc}
   */
  public function getFormId() {
    return "oclc_api.settings_form";
  }

  /**
   * {@inheritDoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);

    $config_names = $this->getEditableConfigNames();
    $form['institution_id'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Institution ID'),
      '#default_value' => $this->config($config_names['default'])
        ->get('institution_id'),
    ];

    return $form;
  }

  /**
   * {@inheritDoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $form_state->cleanValues();
    $this->save($form, $form_state);
    parent::submitForm($form, $form_state);
  }

  /**
   * Save to settings.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The current state of the form.
   */
  protected function save(array &$form, FormStateInterface $form_state) {
    $config = $this->config($this->getEditableConfigNames()['default']);
    foreach ($form_state->getValues() as $key => $value) {
      $config->set($key, $value);
    }
    $config->save();
  }

}
