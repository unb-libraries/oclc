<?php

namespace Drupal\oclc_api\Oclc;

use Drupal\key\KeyInterface;

/**
 * Interface for OCLC API authorizers.
 *
 * @package Drupal\oclc_api\Oclc
 */
interface OclcAuthorizationInterface {

  /**
   * Set the key entity.
   *
   * @param \Drupal\key\KeyInterface $key
   *   A key entity.
   */
  public function setKey(KeyInterface $key);

  /**
   * Retrieve the API scopes.
   *
   * @return array
   *   An array of OCLC API plugin IDs.
   */
  public function getApis();

  /**
   * Set the given API scopes.
   *
   * @param array $scopes
   *   An array of OCLC API plugin IDs.
   */
  public function setApis(array $scopes);

  /**
   * Add the given scope.
   *
   * @param string $api
   *   An OCLC API plugin ID.
   */
  public function addApi(string $api);

  /**
   * Retrieve an authorization token.
   *
   * @return string|false
   *   An authorization token string.
   *   FALSE if authorization failed.
   */
  public function getToken();

}
