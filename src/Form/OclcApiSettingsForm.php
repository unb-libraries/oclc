<?php

namespace Drupal\oclc_api\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\oclc_api\Config\OclcApiConfigInterface;

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
      'default' => OclcApiConfigInterface::CONFIG_ID,
    ];
  }

  /**
   * {@inheritDoc}
   */
  public function getFormId() {
    return "{$this->getEditableConfigNames()['default']}_form";
  }

  /**
   * {@inheritDoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form = parent::buildForm($form, $form_state);

    $config_names = $this->getEditableConfigNames();
    $form[OclcApiConfigInterface::INSTITUTION_ID] = [
      '#type' => 'textfield',
      '#title' => $this->t('Institution ID'),
      '#default_value' => $this->config($config_names['default'])
        ->get(OclcApiConfigInterface::INSTITUTION_ID),
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
