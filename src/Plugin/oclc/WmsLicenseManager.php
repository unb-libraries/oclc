<?php

namespace Drupal\oclc_api\Plugin\oclc;

use Drupal\oclc_api\Annotation\OclcApi;
use Psr\Http\Message\ResponseInterface;

/**
 * Plugin to interact with the WMS License Manager API.
 *
 * @OclcApi(
 *   id = "wms_license_manager",
 *   label = @Translation("WMS License Manager API"),
 *   endpoints = {
 *     "read" = "https://{@institution_id}.share.worldcat.org/license-manager/license/data/{@license_id}",
 *     "search" = "https://{@institution_id}.share.worldcat.org/license-manager/license/search?q={@query}&licenseStatus={@licenseStatus}&subscriptionType=@{subscriptionType}&fetchDetails={@fetchDetails}&startIndex={@startIndex}&itemsPerPage={@itemsPerPage}",
 *     "list" = "https://{@institution_id}.share.worldcat.org/license-manager/license/list?fetchDetails={@fetchDetails}&startIndex={@startIndex}&itemsPerPage={@itemsPerPage}"
 *   },
 *   scopes = {
 *     "WMS_LMAN",
 *     "context:{@institution_id}"
 *   }
 * )
 *
 * @link https://developer.api.oclc.org/lman
 *
 * @package Drupal\oclc_api\Plugin\oclc
 */
class WmsLicenseManager extends OclcApiBase {

  const INSTITUTION_ID = '133054';

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
   * Retrieve the OCLC institution ID.
   *
   * @return string
   *   A string.
   */
  protected function getInstitutionId() {
    return self::INSTITUTION_ID;
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
    ];
  }

  /**
   * {@inheritDoc}
   */
  protected function buildResponse(ResponseInterface $response) {
    return $response->getBody()
      ->getContents();
  }

}
