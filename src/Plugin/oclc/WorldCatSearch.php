<?php

namespace Drupal\oclc_api\Plugin\oclc;

use Drupal\oclc_api\Annotation\OclcApi;
use Psr\Http\Message\ResponseInterface;

/**
 * Plugin to interact with the WorldCat Search API.
 *
 * @OclcApi(
 *   id = "worldcat_search",
 *   label = @Translation("WorldCat Search API"),
 *   endpoints = {
 *     "get-libraries-by-oclc-number" = "http://www.worldcat.org/webservices/catalog/content/libraries/{@oclc_id}?format=json&frbrGrouping=off&location={@location}",
 *   }
 * )
 *
 * @link https://platform.worldcat.org/api-explorer/apis/wcapi
 *
 * @package Drupal\oclc_api\Plugin\oclc
 */
class WorldCatSearch extends OclcApiBase {

  /**
   * {@inheritDoc}
   */
  protected function buildHeaders() {
    return ['wskey' => $this->oclcAuthorizer()->getWskey()];
  }

  /**
   * {@inheritDoc}
   */
  protected function buildResponse(ResponseInterface $response) {
    return json_decode($response->getBody()->getContents());
  }

}
