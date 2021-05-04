<?php

namespace Drupal\oclc_api\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Annotation for OCLC API plugins.
 *
 * @package Drupal\label_printing\Annotation
 *
 * @Annotation
 */
class OclcApi extends Plugin {

  /**
   * Retrieve the API endpoints.
   *
   * @return array
   *   An array of the form ENDPOINT_ID => URL.
   */
  public function getEndpoints() {
    return $this->definition['endpoints'];
  }

  /**
   * Retrieve all required authorization scopes.
   *
   * @return array
   *   An array of strings.
   */
  public function getScopes() {
    return $this->definition['scopes'];
  }

}
