<?php

namespace Drupal\oclc_api\Plugin\oclc;

use Drupal\oclc_api\Annotation\OclcApi;
use Psr\Http\Message\ResponseInterface;

/**
 * Plugin to interact with the WorldCat Metadata API.
 *
 * @OclcApi(
 *   id = "worldcat_metadata",
 *   label = @Translation("WorldCat Metadata API"),
 *   endpoints = {
 *     "get-bibs-summary-holdings" = {
 *       "url" = "https://metadata.api.oclc.org/worldcat/search/bibs-summary-holdings",
 *       "query" = {
 *         "oclcNumber" = "",
 *         "heldInCountry" = "",
 *       },
 *     },
 *     "search-my-holdings" = {
 *       "url" = "https://metadata.api.oclc.org/worldcat/search/my-holdings",
 *       "query" = {
 *         "barcode" = "",
 *       },
 *     },
 *   },
 *   scopes = {
 *     "WorldCatMetadataAPI:view_summary_holdings",
 *     "WorldCatMetadataAPI:view_my_holdings",
 *   }
 * )
 *
 * @link https://developer.api.oclc.org/wc-metadata-v2
 *
 * @package Drupal\oclc_api\Plugin\oclc
 */
class WorldCatMetadata extends OclcApiBase {

  /**
   * {@inheritDoc}
   */
  protected function buildHeaders() {
    $token = $this->oclcAuthorizer()
      ->getToken();
    return parent::buildHeaders() + [
      'Authorization' => "Bearer {$token}",
    ];
  }

  /**
   * {@inheritDoc}
   */
  protected function buildUrlQuery(array $endpoint, array $params) {
    if (empty($params['heldInCountry'])) {
      unset($endpoint['query']['heldInCountry']);
      unset($params['heldInCountry']);
    }
    return parent::buildUrlQuery($endpoint, $params);
  }

  /**
   * {@inheritDoc}
   */
  protected function buildResponse(ResponseInterface $response) {
    return json_decode($response->getBody()->getContents());
  }

}
