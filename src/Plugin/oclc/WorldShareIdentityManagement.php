<?php

namespace Drupal\oclc_api\Plugin\oclc;

use Drupal\oclc_api\Annotation\OclcApi;
use Psr\Http\Message\ResponseInterface;

/**
 * Plugin to interact with the WorldShare Identity Management API.
 *
 * @OclcApi(
 *   id = "worldshare_identity_management",
 *   label = @Translation("WorldShare Identity Management API"),
 *   endpoints = {
 *     "read" = "https://{@institution_id}.share.worldcat.org/idaas/scim/v2/Users/{@user_id}",
 *     "search" = "https://{@institution_id}.share.worldcat.org/idaas/scim/v2/Users/.search"
 *   },
 *   scopes = {
 *     "SCIM",
 *     "context:{@institution_id}"
 *   }
 * )
 *
 * @link https://developer.api.oclc.org/idm
 *
 * @package Drupal\oclc_api\Plugin\oclc
 */
class WorldShareIdentityManagement extends OclcApiBase {

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
      'Content-Type' => 'application/scim+json'
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
