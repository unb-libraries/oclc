<?php

namespace Drupal\oclc_api\Plugin\oclc;

use Drupal\Component\Plugin\PluginBase;
use Drupal\oclc_api\Oclc\OclcAuthorizationTrait;
use Psr\Http\Message\ResponseInterface;

/**
 * Base class for OCLC API plugins.
 *
 * @package Drupal\oclc_api\Plugin\oclc
 */
abstract class OclcApiBase extends PluginBase implements OclcApiInterface {

  use OclcAuthorizationTrait;

  /**
   * Inject the http service.
   *
   * @return \GuzzleHttp\Client
   *   An http client object.
   */
  protected function http() {
    return \Drupal::httpClient();
  }

  /**
   * {@inheritDoc}
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    if (isset($configuration['authorization'])) {
      $this->oclcAuthorizer = $configuration['authorization'];
    }
  }

  /**
   * {@inheritDoc}
   */
  public function getScopes() {
    return $this->getPluginDefinition()['scopes'];
  }

  /**
   * {@inheritDoc}
   */
  public function getEndpoints() {
    return $this->getPluginDefinition()['endpoints'];
  }

  /**
   * {@inheritDoc}
   */
  public function get(string $endpoint, array $params) {
    if ($url = $this->buildUrl($endpoint, $params)) {
      return $this->buildResponse($this->http()->get($url, [
        'headers' => $this->buildHeaders(),
      ]));
    }
    return FALSE;
  }

  /**
   * Build a URL for the given endpoint.
   *
   * @param string $endpoint
   *   The endpoint identifier.
   * @param array $params
   *   An array of URL parameters and their values.
   *
   * @return string|false
   *   A URL string. FALSE if a valid URL could not
   *   be created from the given parameters.
   */
  protected function buildUrl(string $endpoint, array $params) {
    if (!array_key_exists($endpoint, $this->getEndpoints())) {
      return FALSE;
    }
    $url = $this->getEndpoints()[$endpoint];
    foreach ($params as $placeholder => $value) {
      $url = str_replace("{@{$placeholder}}", $value, $url);
    }
    if (preg_match('/{@[a-z]+(_[a-z]+)*}/', $url)) {
      return FALSE;
    }
    return $url;
  }

  /**
   * Build HTTP request headers.
   *
   * @return string[]
   *   An array of the form HTTP_HEADER => VALUE.
   */
  protected function buildHeaders() {
    return [];
  }

  /**
   * Parse the HTTP response.
   *
   * @param \Psr\Http\Message\ResponseInterface $response
   *   The response.
   *
   * @return mixed
   *   The content of the response body.
   */
  abstract protected function buildResponse(ResponseInterface $response);

}
