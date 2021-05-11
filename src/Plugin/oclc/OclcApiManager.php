<?php

namespace Drupal\oclc_api\Plugin\oclc;

use Drupal\Core\Cache\CacheBackendInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Plugin\DefaultPluginManager;
use Drupal\oclc_api\Annotation\OclcApi;
use Drupal\oclc_api\Config\OclcApiConfigInterface;

/**
 * Plugin manager for OCLC API plugins.
 *
 * @package Drupal\oclc_api\Plugin\oclc
 */
class OclcApiManager extends DefaultPluginManager implements OclcApiManagerInterface {

  /**
   * OCLC_API config.
   *
   * @var \Drupal\oclc_api\Config\OclcApiConfigInterface
   */
  protected $oclcApiConfig;

  /**
   * @return mixed
   */
  protected function oclcApiConfig() {
    return $this->oclcApiConfig;
  }

  /**
   * Constructs a new OclcApiManager instance.
   *
   * @param \Traversable $namespaces
   *   An object that implements \Traversable which contains the root paths
   *   keyed by the corresponding namespace to look for plugin implementations.
   * @param \Drupal\Core\Cache\CacheBackendInterface $cache_backend
   *   Cache backend instance to use.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler to invoke the alter hook with.
   * @param \Drupal\oclc_api\Config\OclcApiConfigInterface $oclc__api_config
   *   An oclc_api config object.
   */
  public function __construct(\Traversable $namespaces, CacheBackendInterface $cache_backend, ModuleHandlerInterface $module_handler, OclcApiConfigInterface $oclc__api_config) {
    parent::__construct(
      'Plugin/oclc',
      $namespaces,
      $module_handler,
      OclcApiInterface::class,
      OclcApi::class);
    $this->alterInfo('oclc_api__info');
    $this->setCacheBackend($cache_backend, 'oclc_api__plugins');
    $this->oclcApiConfig = $oclc__api_config;
  }

  /**
   * {@inheritDoc}
   */
  public function createInstance($plugin_id, array $configuration = []) {
    // @todo Cache building instances.
    if ($institution_id = $this->oclcApiConfig()->getInstitutionId()) {
      $configuration[OclcApiConfigInterface::INSTITUTION_ID] = $institution_id;
    }
    if ($data_center = $this->oclcApiConfig()->getDataCenter()) {
      $configuration[OclcApiConfigInterface::DATACENTER] = $data_center;
    }
    return parent::createInstance($plugin_id, $configuration);
  }

}
