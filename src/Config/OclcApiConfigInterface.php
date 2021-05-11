<?php

namespace Drupal\oclc_api\Config;

/**
 * Interface for oclc_api module settings.
 *
 * @package Drupal\oclc_api\Config
 */
interface OclcApiConfigInterface {

  const CONFIG_ID = 'oclc_api.settings';
  const INSTITUTION_ID = 'institution_id';
  const DATACENTER = 'datacenter';

  /**
   * Get the institution ID.
   *
   * @return string
   *   A string.
   */
  public function getInstitutionId();

  /**
   * Get the datacenter identifier.
   *
   * @return string
   */
  public function getDataCenter();

}
