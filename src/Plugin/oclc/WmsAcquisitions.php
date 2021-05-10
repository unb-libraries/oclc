<?php

namespace Drupal\oclc_api\Plugin\oclc;

use Drupal\oclc_api\Annotation\OclcApi;
use Psr\Http\Message\ResponseInterface;

/**
 * Plugin to interact with the WMS Acquisitions API.
 *
 * @OclcApi(
 *   id = "wms_acquisitions",
 *   label = @Translation("WMS Acquisitions API"),
 *   endpoints = {
 *     "read-budget" = "https://{@institution_id}.share.worldcat.org/acquisitions/budget/data/{@budget_id}",
 *     "read-fund" = "https://{@institution_id}.share.worldcat.org/acquisitions/fund/data/{@fund_id}",
 *     "search-funds" = "https://{@institution_id}.share.worldcat.org/acquisitions/fund/search?q=budgetPeriod:{@budget_id}&startIndex={@start_index}&itemsPerPage=25",
 *   },
 *   scopes = {
 *     "WMS_ACQ",
 *     "context:{@institution_id}"
 *   }
 * )
 *
 * @link https://developer.api.oclc.org/wms-acq-invoices
 *
 * @package Drupal\oclc_api\Plugin\oclc
 */
class WmsAcquisitions extends OclcApiBase {

  /**
   * {@inheritDoc}
   */
  public function getScopes() {
    foreach ($scopes = parent::getScopes() as $index => $scope) {
      $scopes[$index] = str_replace("{@institution_id}", $this->getInstitutionId(), $scope);
    }
    return $scopes;
  }

  /**
   * {@inheritDoc}
   */
  protected function buildUrl(string $endpoint, array $params) {
    $params += ['institution_id' => $this->getInstitutionId()];
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
    return json_decode($response->getBody()->getContents());
  }

}
