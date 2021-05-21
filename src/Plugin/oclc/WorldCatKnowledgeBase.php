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
 *         "wskey" = "",
 *       },
 *     },
 *     "read-collection" = {
 *       "url" = "https://worldcat.org/webservices/kb/rest/collections/{@collection_uid}",
 *       "query" = {
 *         "alt"= "json",
 *         "wskey" = "",
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
 *         "wskey" = "",
 *       },
 *     },
 *     "read-entry" = {
 *       "url" = "https://worldcat.org/webservices/kb/rest/entries/{@entry_uid}",
 *       "query" = {
 *         "alt"= "json",
 *         "wskey" = "",
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
 *         "wskey" = "",
 *       },
 *     },
 *     "read-provider" = {
 *       "url" = "https://worldcat.org/webservices/kb/rest/providers/{@provider_uid}",
 *       "query" = {
 *         "alt"= "json",
 *         "wskey" = "",
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
  protected function buildUrl(string $endpoint, array $params) {
    $params += ['wskey' => $this->oclcAuthorizer()->getWskey()];
    return parent::buildUrl($endpoint, $params);
  }

  /**
   * {@inheritDoc}
   */
  protected function buildResponse(ResponseInterface $response) {
    return json_decode($response->getBody()->getContents());
  }

}
