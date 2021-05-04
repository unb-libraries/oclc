<?php

namespace Drupal\oclc_api\Plugin\oclc;

use Drupal\oclc_api\Annotation\OclcApi;
use Psr\Http\Message\ResponseInterface;

/**
 * Plugin to interact with the WorldCat Knowledge Base API.
 *
 * @OclcApi(
 *   id = "worldcat_knowledge_base",
 *   label = @Translation("WorldCat Knowledge Base API"),
 *   endpoints = {
 *     "search-collections" = "https://worldcat.org/webservices/kb/rest/collections/search?alt=json&q={@query}&title={@title}&provider_uid={@provider_uid}&collection_uid={@collection_uid}&search-type={@search-type}&startIndex={@startIndex}&itemsPerPage={@itemsPerPage}&orderBy={@orderBy}",
 *     "read-collection" = "https://worldcat.org/webservices/kb/rest/collections/{@collection_uid}?alt=json",
 *     "search-entries" = "https://worldcat.org/webservices/kb/rest/entries/search?alt=json&q={@query}&title={@title}&provider_uid={@provider_uid}&collection_uid={@collection_uid}&issn={@issn}&isbn={@isbn}&oclcnum={@oclcnum}&content={@content}&search-type={@search-type}&startIndex={@startIndex}&itemsPerPage={@itemsPerPage}&orderBy={@orderBy}",
 *     "read-entry" = "https://worldcat.org/webservices/kb/rest/entries/{@entry_uid}?alt=json",
 *     "search-providers" = "https://worldcat.org/webservices/kb/rest/providers/search?alt=json&q={@query}&title={@title}&provider_uid={@provider_uid}&startIndex={@startIndex}&itemsPerPage={@itemsPerPage}&orderBy={@orderBy}",
 *     "read-provider" = "https://worldcat.org/webservices/kb/rest/providers/{@provider_uid}?alt=json",
 *   }
 * )
 *
 * @link https://platform.worldcat.org/api-explorer/apis/kbwcapi
 *
 * @package Drupal\oclc_api\Plugin\oclc
 */
class WorldCatKnowledgeBase extends OclcApiBase {

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
