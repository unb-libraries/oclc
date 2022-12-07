<?php

namespace Drupal\oclc_api\Plugin\oclc;

use Drupal\oclc_api\Annotation\OclcApi;
use Psr\Http\Message\ResponseInterface;

/**
 * Plugin to interact with the WorldCat Search API (V2).
 *
 * @OclcApi(
 *   id = "worldcat_search_v2",
 *   label = @Translation("WorldCat Search API (V2)"),
 *   endpoints = {
 *     "get-bibliographic-resource" = "https://americas.discovery.api.oclc.org/worldcat/search/v2/bibs/{@oclcNumber}",
 *     "get-bibs-summary-holdings" = {
 *       "url" = "https://americas.discovery.api.oclc.org/worldcat/search/v2/bibs-summary-holdings",
 *       "query" = {
 *         "oclcNumber" = "",
 *       },
 *     },
 *   },
 *   scopes = {
 *     "wcapi",
 *   }
 * )
 *
 * @link https://developer.api.oclc.org/wcv2
 *
 * @package Drupal\oclc_api\Plugin\oclc
 */
class WorldCatSearchV2 extends OclcApiBase {

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
  protected function buildResponse(ResponseInterface $response) {
    return json_decode($response->getBody()->getContents());
  }

}
