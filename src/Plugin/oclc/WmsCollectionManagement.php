<?php

namespace Drupal\oclc_api\Plugin\oclc;

use Drupal\oclc_api\Annotation\OclcApi;
use Psr\Http\Message\ResponseInterface;

/**
 * Plugin to interact with the WMS Collection Management API.
 *
 * @OclcApi(
 *   id = "wms_collection_management",
 *   label = @Translation("WMS Collection Management"),
 *   endpoints = {
 *     "barcode-search" = "https://circ.{@datacenter}.worldcat.org/LHR?q=barcode:{@barcode}",
 *   },
 *   scopes = {
 *     "WMS_COLLECTION_MANAGEMENT",
 *   }
 * )
 *
 * @link https://developer.api.oclc.org/wms-collection-management
 *
 * @package Drupal\oclc_api\Plugin\oclc
 */
class WmsCollectionManagement extends OclcApiBase {

  /**
   * {@inheritDoc}
   */
  protected function buildUrl(string $endpoint, array $params) {
    $params += ['datacenter' => $this->defaultDatacenter()];
    return parent::buildUrl($endpoint, $params);
  }

  /**
   * {@inheritDoc}
   */
  protected function buildHeaders() {
    $token = $this->oclcAuthorizer()
      ->getToken();
    return parent::buildHeaders() + [
      'Authorization' => "Bearer {$token}",
      'Accept' => 'application/json',
    ];
  }

  /**
   * {@inheritDoc}
   */
  protected function buildResponse(ResponseInterface $response) {
    $results = [];
    foreach (\json_decode($response->getBody(), JSON_OBJECT_AS_ARRAY)['entry'] as $entry) {
      $oclc_id = $this->extractOclcId($entry);
      $results[$oclc_id] = $entry + ['oclcId' => $oclc_id];
    }
    return $results;
  }

  /**
   * Extract the OCLC ID from the given response record.
   *
   * @param array $entry
   *   An array.
   *
   * @return int
   *   An integer.
   */
  protected function extractOclcId(array $entry) {
    preg_match('#^/bibs/(?P<oclc_id>[0-9]+)$#', $entry['bib'], $matches);
    return $matches['oclc_id'];
  }

}
