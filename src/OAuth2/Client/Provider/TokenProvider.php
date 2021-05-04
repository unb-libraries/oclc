<?php

namespace Drupal\oclc_api\OAuth2\Client\Provider;

use Drupal\Core\State\StateInterface;
use League\OAuth2\Client\Provider\GenericProvider;
use League\OAuth2\Client\Token\AccessToken;

/**
 * Token provider for OCLC APIs.
 *
 * @package Drupal\oclc_api\OAuth2\Client\Provider
 */
class TokenProvider {

  /**
   * The state service.
   *
   * @var \Drupal\Core\State\StateInterface
   */
  protected $state;

  /**
   * An access token.
   *
   * @var \League\OAuth2\Client\Token\AccessToken
   */
  protected $token;

  /**
   * Get the state service.
   *
   * @return \Drupal\Core\State\StateInterface
   *   A state object.
   */
  protected function getState() {
    return $this->state;
  }

  /**
   * Construct an TokenProvider instance.
   *
   * @param \Drupal\Core\State\StateInterface $state
   *   A state object.
   */
  public function __construct(StateInterface $state) {
    $this->state = $state;
  }

  /**
   * {@inheritDoc}
   */
  public function getAccessToken($grant, array $options = []) {
    $scopes = $options['scope'];
    if (!isset($this->token)) {
      $state_key = 'oclc_token.' . implode('_', $scopes);
      if ($token = $this->getState()->get($state_key)) {
        $this->token = new AccessToken(\json_decode($token));
      }
      else {
        $token = parent::getAccessToken($grant, ['scope' => implode(' ', $scopes)]);
        $this->getState()->set($state_key, \json_encode([
          'access_token' => $token->getToken(),
          'refresh_token' => $token->getRefreshToken(),
          'resource_owner_id' => $token->getResourceOwnerId(),
          'expires' => $token->getExpires(),
        ]));
      }
      $this->token = $token;
    }
    return $this->token;
  }

}
