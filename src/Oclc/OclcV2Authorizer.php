<?php

namespace Drupal\oclc_api\Oclc;

use Drupal\key\KeyInterface;
use Drupal\oclc_api\Plugin\oclc\OclcApiManagerInterface;
use Drupal\oclc_api\Plugin\oclc\OclcPluginManagerTrait;
use League\OAuth2\Client\OptionProvider\HttpBasicAuthOptionProvider;
use League\OAuth2\Client\Provider\GenericProvider;
use League\OAuth2\Client\Token\AccessTokenInterface;
use League\OAuth2\Client\Token\AccessToken;

/**
 * Authorizer for OCLC API.
 *
 * @package Drupal\oclc_api\oclc
 */
class OclcV2Authorizer implements OclcAuthorizationInterface {

  use OclcPluginManagerTrait;

  /**
   * A key entity holding OCLC credentials.
   *
   * @var \Drupal\key\KeyInterface
   */
  protected $key;

  /**
   * The OCLC APIs that define the scope for authorization.
   *
   * @var \Drupal\oclc_api\Plugin\oclc\OclcApiInterface[]
   */
  protected $apis;

  /**
   * The access token.
   *
   * @var League\OAuth2\Client\Token\AccessTokenInterface
   */
  protected $token;

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
    return $this->apis;
  }

  /**
   * {@inheritDoc}
   */
  public function setApis(array $apis) {
    $this->apis = [];
    foreach ($apis as $api) {
      $this->addApi($api);
    }
  }

  /**
   * {@inheritDoc}
   */
  public function addApi($api) {
    if ($plugin = $this->tryGetApi($api)) {
      $this->apis[] = $plugin;
    }

  }

  /**
   * Create a new OclcAuthorizer instance.
   *
   * @param \Drupal\key\KeyInterface $key
   *   A key entity holding authorization information.
   * @param \Drupal\oclc_api\Plugin\oclc\OclcApiManagerInterface $oclc_api_manager
   *   A plugin manager object.
   * @param array $apis
   *   An array of OCLC API plugin IDs with which this
   *   authorizer should authorize.
   */
  public function __construct(KeyInterface $key, OclcApiManagerInterface $oclc_api_manager, array $apis = []) {
    $this->key = $key;
    $this->oclcApiManager = $oclc_api_manager;
    $this->setApis($apis);
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
   * {@inheritDoc}
   */
  public function getToken() {
    if (!$api_key = $this->getAuthOptions()) {
      return FALSE;
    }

    if (!isset($this->token)) {
      $this->token = $this->getCachedToken();
    }

    if (!$this->token || ($this->token->getExpires() && $this->token->hasExpired())) {
      $this->token = $this->getNewToken($api_key);
    }

    return $this->token
      ->getToken();
  }

  /**
   * Request a token from the Drupal state.
   *
   * @return false|\League\OAuth2\Client\Token\AccessToken
   *   An access token.
   */
  protected function getCachedToken() {
    // @todo Refactor call to Drupal::state().
    if ($token = \Drupal::state()->get($this->getTokenCacheKey())) {
      return new AccessToken(\json_decode($token, JSON_OBJECT_AS_ARRAY));
    }
    return FALSE;
  }

  /**
   * Get a new access token.
   *
   * @param array $api_key
   *   An API key.
   *
   * @return League\OAuth2\Client\Token\AccessTokenInterface
   *   An OAuth2 access token.
   */
  protected function getNewToken(array $api_key) {
    $scopes = $this->getScopes();
    $provider = new GenericProvider($api_key, ['optionProvider' => new HttpBasicAuthOptionProvider()]);
    $token = $provider->getAccessToken($this->getGrantType(), [
      'scope' => implode(' ', $scopes),
    ]);
    $this->setCachedToken($token);
    return $token;
  }

  /**
   * Write the given token to the Drupal state.
   *
   * @param \League\OAuth2\Client\Token\AccessTokenInterface $token
   *   An access token.
   */
  protected function setCachedToken(AccessTokenInterface $token) {
    // @todo Refactor call to Drupal::state().
    \Drupal::state()->set($this->getTokenCacheKey(), \json_encode([
      'access_token' => $token->getToken(),
      'refresh_token' => $token->getRefreshToken(),
      'resource_owner_id' => $token->getResourceOwnerId(),
      'expires' => $token->getExpires(),
    ]));
  }

  /**
   * Get the Drupal state key for an OCLC API token.
   *
   * @return string
   *   A string.
   */
  protected function getTokenCacheKey() {
    return 'oclc_token.' . implode('_', $this->getScopes());
  }

  /**
   * Retrieve an array of authorization information.
   *
   * @return array
   *   An array of key-value paris.
   */
  protected function getAuthOptions() {
    if (array_key_exists('authOptions', $key = $this->getKeyValue())) {
      return $key['authOptions'];
    }
    return [];
  }

  /**
   * Retrieve the grant type.
   *
   * @return string
   *   A string.
   */
  protected function getGrantType() {
    if (array_key_exists('grantType', $key = $this->getKeyValue())) {
      return $key['grantType'];
    }
    return 'client_credentials';
  }

  /**
   * Retrieve the authorization scopes.
   *
   * @return array
   *   An array of strings.
   */
  public function getScopes() {
    $scopes = [];
    foreach ($this->getApis() as $api) {
      $scopes = array_merge($scopes, $api->getScopes());
    }
    return $scopes;
  }

}
