<?php

namespace Drupal\oclc_api\Plugin\oclc;

use Drupal\oclc_api\Annotation\OclcApi;
use Psr\Http\Message\ResponseInterface;

/**
 * Plugin to interact with the WMS Availability API.
 *
 * @OclcApi(
 *   id = "wms_availability",
 *   label = @Translation("WMS Availability API"),
 *   endpoints = {
 *     "read" = "https://worldcat.org/circ/availability/sru/service?x-registryId={@institution_id}&query=no:{@oclc_id}",
 *   },
 *   scopes = {
 *     "WMS_Availability",
 *     "context:{@institution_id}"
 *   }
 * )
 *
 * @link https://developer.api.oclc.org/wms-availability
 *
 * @package Drupal\oclc_api\Plugin\oclc
 */
class WmsAvailability extends OclcApiBase {

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
