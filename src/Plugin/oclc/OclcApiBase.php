<?php

namespace Drupal\oclc_api\Plugin\oclc;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\oclc_api\Config\OclcApiConfigInterface;
use Drupal\oclc_api\Oclc\OclcAuthorizationTrait;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Base class for OCLC API plugins.
 *
 * @package Drupal\oclc_api\Plugin\oclc
 */
abstract class OclcApiBase extends PluginBase implements OclcApiInterface, ContainerFactoryPluginInterface {

  use OclcAuthorizationTrait;

  /**
   * The Http service.
   *
   * @var \GuzzleHttp\ClientInterface
   */
  protected $http;

  /**
   * Inject the http service.
   *
   * @return \GuzzleHttp\ClientInterface
   *   An http client object.
   */
  protected function http() {
    return $this->http;
  }

  /**
   * Get the institution ID.
   *
   * @return false|string
   *   A string. FALSE if no institution ID is configured.
   */
  protected function getInstitutionId() {
    if (isset($this->configuration[OclcApiConfigInterface::INSTITUTION_ID])) {
      return $this->configuration[OclcApiConfigInterface::INSTITUTION_ID];
    }
    return FALSE;
  }

  /**
   * Constructs an OCLC plugin instance.
   *
   * @param array $configuration
   *   A configuration array containing information about the plugin instance.
   * @param string $plugin_id
   *   The plugin_id for the plugin instance.
   * @param mixed $plugin_definition
   *   The plugin implementation definition.
   * @param \GuzzleHttp\ClientInterface $http
   *   An HTTP client.
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, ClientInterface $http) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->http = $http;
    if (isset($configuration['authorization'])) {
      $this->oclcAuthorizer = $configuration['authorization'];
    }
  }

  /**
   * {@inheritDoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static($configuration, $plugin_id, $plugin_definition, $container->get('http_client'));
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
