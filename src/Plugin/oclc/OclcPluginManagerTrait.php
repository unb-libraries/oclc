<?php

namespace Drupal\oclc_api\Plugin\oclc;

/**
 * Provides dependency injection for OCLC API plugins.
 *
 * @package Drupal\oclc_api\Plugin\oclc
 */
trait OclcPluginManagerTrait {

  /**
   * An OCLC api plugin manager.
   *
   * @var \Drupal\oclc_api\Plugin\oclc\OclcApiManagerInterface
   */
  protected $oclcApiManager;

  /**
   * Inject the OCLC API plugin manager service.
   *
   * @return \Drupal\oclc_api\Plugin\oclc\OclcApiManagerInterface
   *   An OCLC API plugin manager object.
   */
  protected function oclcApiManager() {
    return $this->oclcApiManager;
  }

  /**
   * Retrieve the API plugin with the given ID.
   *
   * @param string $id
   *   The plugin ID of the requested API.
   * @param array $configuration
   *   Plugin configuration.
   *
   * @return \Drupal\oclc_api\Plugin\oclc\OclcApiInterface
   *   An OCLC API object.
   *
   * @throws \Drupal\Component\Plugin\Exception\PluginException
   */
  protected function oclcApi(string $id, array $configuration = []) {

    /** @var \Drupal\oclc_api\Plugin\oclc\OclcApiInterface $api */
    $api = $this->oclcApiManager()
      ->createInstance($id, $configuration);
    return $api;
  }

}
