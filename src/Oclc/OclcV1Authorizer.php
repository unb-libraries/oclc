<?php

namespace Drupal\oclc_api\Oclc;

use Drupal\key\KeyInterface;
use Drupal\oclc_api\Plugin\oclc\OclcApiManagerInterface;
use Drupal\oclc_api\Plugin\oclc\OclcPluginManagerTrait;

/**
 * Authorizer for OCLC API (WSKey v1).
 *
 * @package Drupal\oclc_api\oclc
 */
class OclcV1Authorizer implements OclcAuthorizationInterface {

  use OclcPluginManagerTrait;

  /**
   * A key entity holding OCLC credentials.
   *
   * @var \Drupal\key\KeyInterface
   */
  protected $key;

  /**
   * Retrieve the key entity.
   *
   * @return \Drupal\key\KeyInterface
   *   A key entity.
   */
  protected function getKey() {
    return $this->key;
  }

  /**
   * {@inheritDoc}
   */
  public function setKey(KeyInterface $key) {
    $this->key = $key;
  }

  /**
   * {@inheritDoc}
   */
  public function getApis() {
  }

  /**
   * {@inheritDoc}
   */
  public function setApis(array $apis) {
  }

  /**
   * {@inheritDoc}
   */
  public function addApi($api) {
  }

  /**
   * {@inheritDoc}
   */
  public function getToken() {
  }

  /**
   * Create a new OclcAuthorizer instance.
   *
   * @param \Drupal\key\KeyInterface $key
   *   A key entity holding authorization information.
   * @param \Drupal\oclc_api\Plugin\oclc\OclcApiManagerInterface $oclc_api_manager
   *   A plugin manager object.
   */
  public function __construct(KeyInterface $key, OclcApiManagerInterface $oclc_api_manager) {
    $this->key = $key;
    $this->oclcApiManager = $oclc_api_manager;
  }

  /**
   * Retrieve the definition of the given OCLC API plugin.
   *
   * @param string $plugin_id
   *   A valid plugin ID.
   *
   * @return \Drupal\oclc_api\Plugin\oclc\OclcApiInterface|false
   *   A plugin definition array.
   *   FALSE if a plugin for the given ID
   *   could not be found.
   */
  protected function tryGetApi(string $plugin_id) {
    try {
      /** @var \Drupal\oclc_api\Plugin\oclc\OclcApiInterface $api */
      $api = static::oclcApiManager()
        ->createInstance($plugin_id);
      return $api;
    }
    catch (\Exception $e) {
      return FALSE;
    }
  }

  /**
   * Retrieve the value of the key entity as an array of key-value pairs.
   *
   * @return array
   *   An array of key-value pairs.
   */
  protected function getKeyValue() {
    return \json_decode($this->getKey()->getKeyValue(), JSON_OBJECT_AS_ARRAY);
  }

  /**
   * Retrieve the WSKey.
   *
   * @return string
   *   A string.
   */
  public function getWskey() {
    $key = $this->getKeyValue();
    return $key['wskey'];
  }

}
