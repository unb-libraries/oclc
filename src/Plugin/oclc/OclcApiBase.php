<?php

namespace Drupal\oclc_api\Plugin\oclc;

use Drupal\Component\Plugin\PluginBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\oclc_api\Config\OclcApiConfigInterface;
use Drupal\oclc_api\Oclc\OclcAuthorizationTrait;
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
   * @param string $endpoint_id
   *   The endpoint identifier.
   * @param array $params
   *   An array of URL parameters and their values.
   *
   * @return string|false
   *   A URL string. FALSE if a valid URL could not
   *   be created from the given parameters.
   */
  protected function buildUrl(string $endpoint_id, array $params) {
    if (!array_key_exists($endpoint_id, $this->getEndpoints())) {
      return FALSE;
    }

    $endpoint = $this->getEndpoints()[$endpoint_id];
    if (!is_array($endpoint)) {
      $endpoint = [
        'url' => $endpoint,
        'query' => [],
      ];
    }
    $base_url = $this->buildBaseUrl($endpoint, $params);
    $query = $this->buildUrlQuery($endpoint, $params);

    if (!empty($query)) {
      return "$base_url?" . http_build_query($query);
    }
    return $base_url;
  }

  /**
   * Resolve any parameterized parts of the base URL.
   *
   * @param array $endpoint
   *   The endpoint.
   * @param array $params
   *   An array of parameters.
   *
   * @return string
   *   A string.
   */
  protected function buildBaseUrl(array $endpoint, array $params) {
    return $this->renderPlaceholder($endpoint['url'], $params);
  }

  /**
   * Resolve parameterized parts of each query parameter.
   *
   * @param array $endpoint
   *   The endpoint.
   * @param array $params
   *   An array of parameters.
   *
   * @return array
   *   An array of query parameters and values.
   */
  protected function buildUrlQuery(array $endpoint, array $params) {
    $query = $endpoint['query'];
    foreach (array_keys($query) as $arg) {
      if (array_key_exists($arg, $params)) {
        $query[$arg] = $params[$arg];
      }
      $query[$arg] = $this
        ->renderPlaceholder($query[$arg], $params);
    }
    return $query;
  }

  /**
   * Replaces any placeholders in the subject with a matching value.
   *
   * @param string $subject
   *   The subject.
   * @param array $placeholders
   *   Values for each possible placeholder.
   *
   * @return string
   *   The subject string after each placeholder has been replaced.
   */
  protected function renderPlaceholder(string $subject, array $placeholders) {
    preg_match_all('/{@(?<placeholder>[a-zA-Z]+([_-][a-zA-Z]+)*)}/', $subject, $matches);
    foreach ($matches['placeholder'] as $placeholder) {
      if (array_key_exists($placeholder, $placeholders)) {
        $subject = str_replace("{@{$placeholder}}", $placeholders[$placeholder], $subject);
      }
    }
    return $subject;
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
