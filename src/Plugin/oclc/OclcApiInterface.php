<?php

namespace Drupal\oclc_api\Plugin\oclc;

use Drupal\Component\Plugin\PluginInspectionInterface;

/**
 * Interface for OCLC API plugins.
 *
 * @package Drupal\oclc_api\Plugin\oclc
 */
interface OclcApiInterface extends PluginInspectionInterface {

  /**
   * Retrieve authorization scopes this API requires.
   *
   * @return array
   *   An array of strings.
   */
  public function getScopes();

  /**
   * Retrieve all endpoints served by this API.
   *
   * @return array
   *   An array of the form ENDPOINT_ID => URL.
   */
  public function getEndpoints();

  /**
   * Request data from the given endpoint.
   *
   * @param string $endpoint
   *   The endpoint to request.
   * @param array $params
   *   (optional) Parameters, required or optional,
   *   which the given endpoint supports.
   *
   * @return mixed
   *   An object.
   */
  public function get(string $endpoint, array $params);

}
