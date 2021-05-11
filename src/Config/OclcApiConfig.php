<?php

namespace Drupal\oclc_api\Config;

use Drupal\Core\Config\Config;

/**
 * Implementation of oclc_api specific config.
 *
 * @package Drupal\oclc_api\Config
 */
class OclcApiConfig implements OclcApiConfigInterface {

  /**
   * The config object.
   *
   * @var \Drupal\Core\Config\Config
   */
  protected $config;

  /**
   * Get the config object.
   *
   * @return \Drupal\Core\Config\Config
   *   A config object.
   */
  protected function config() {
    return $this->config;
  }

  /**
   * Constructs a new OclcApiConfig instance.
   *
   * @param \Drupal\Core\Config\Config $oclc_api_config
   *   A config object.
   */
  public function __construct(Config $oclc_api_config) {
    $this->config = $oclc_api_config;
  }

  /**
   * {@inheritDoc}
   */
  public function getInstitutionId() {
    return $this->config()
      ->get(self::INSTITUTION_ID);
  }

  /**
   * {@inheritDoc}
   */
  public function getDataCenter() {
    return $this->config()
      ->get(self::DATACENTER);
  }


}
