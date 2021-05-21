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
 *     "search-collections" = {
 *       "url" = "https://worldcat.org/webservices/kb/rest/collections/search",
 *       "query" = {
 *         "alt" = "json",
 *         "q" = "",
 *         "title" = "",
 *         "provider_uid" = "",
 *         "collection_uid" = "",
 *         "search-type" = "",
 *         "startIndex" = "1",
 *         "itemsPerPage" = "25",
 *         "orderBy" = "",
 *       },
 *     },
 *     "read-collection" = {
 *       "url" = "https://worldcat.org/webservices/kb/rest/collections/{@collection_uid}",
 *       "query" = {
 *         "alt"= "json",
 *       },
 *     },
 *     "search-entries" = {
 *       "url" = "https://worldcat.org/webservices/kb/rest/entries/search",
 *       "query" = {
 *         "alt" = "json",
 *         "q" = "",
 *         "title" = "",
 *         "provider_uid" = "",
 *         "collection_uid" = "",
 *         "issn" = "",
 *         "isbn" = "",
 *         "oclcnum" = "",
 *         "content" = "",
 *         "search-type" = "",
 *         "startIndex" = "1",
 *         "itemsPerPage" = "25",
 *         "orderBy" = "",
 *       },
 *     },
 *     "read-entry" = {
 *       "url" = "https://worldcat.org/webservices/kb/rest/entries/{@entry_uid}",
 *       "query" = {
 *         "alt"= "json",
 *       },
 *     },
 *     "search-providers" = {
 *       "url" = "https://worldcat.org/webservices/kb/rest/providers/search",
 *       "query" = {
 *         "alt" = "json",
 *         "q" = "",
 *         "title" = "",
 *         "provider_uid" = "",
 *         "startIndex" = "1",
 *         "itemsPerPage" = "25",
 *         "orderBy" = "",
 *       },
 *     },
 *     "read-provider" = {
 *       "url" = "https://worldcat.org/webservices/kb/rest/providers/{@provider_uid}",
 *       "query" = {
 *         "alt"= "json",
 *       },
 *     },
 *   },
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
