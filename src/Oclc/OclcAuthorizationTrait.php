<?php

namespace Drupal\oclc_api\Oclc;

/**
 * Provides OCLC authorization.
 *
 * @package Drupal\oclc_api\Oclc
 */
trait OclcAuthorizationTrait {

  /**
   * The OCLC authorizer.
   *
   * @var \Drupal\oclc_api\Oclc\OclcAuthorizationInterface
   */
  protected $oclcAuthorizer;

  /**
   * Retrieve the OCLC authorizer.
   *
   * @return \Drupal\oclc_api\Oclc\OclcAuthorizationInterface
   *   An OCLC authorizer object.
   */
  protected function oclcAuthorizer() {
    return $this->oclcAuthorizer;
  }

}
