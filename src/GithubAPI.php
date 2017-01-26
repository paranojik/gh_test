<?php

namespace Drupal\gh_test;

use Drupal\Core\Config\ConfigFactoryInterface;
use GuzzleHttp\Client as GuzzleHttpClient;
use GuzzleHttp\Exception\RequestException;

/**
 * Fetches repository lists via the github API.
 */
class GithubAPI implements GithubAPIInterface {

  /**
   * The github API client ID.
   *
   * @var string
   */
  protected $clientId;

  /**
   * The github API client secret.
   *
   * @var string
   */
  protected $clientSecret;

  /**
   * Constructs a new GithubAPI client.
   *
   * @param \Drupal\Core\Config\ConfigFactoryInterface $config_factory
   *   The config factory.
   */
  public function __construct(ConfigFactoryInterface $config_factory) {
    $this->clientId = $config_factory->get('gh_test.config')->get('client_id');
    $this->clientSecret = $config_factory->get('gh_test.config')
      ->get('client_secret');
  }

  /**
   * Queries the github API and returns the response as list.
   *
   * @param string $url
   *   Github API query URL.
   *
   * @return int|mixed
   *   List of raw objects as returned by the Github API.
   */
  protected function queryGithub($url) {
    $client = new GuzzleHttpClient();
    try {
      $response = $client->get($url, ['http_errors' => FALSE]);
      if ($response->getStatusCode() == 200) {
        $data = $response->getBody();
        $repositories = json_decode($data);
        return $repositories;
      }
      return [];
    }
    catch (RequestException $e) {
      return [];
    }
  }

  /**
   * {@inheritdoc}
   */
  public function getRepositories() {
    $url = "https://api.github.com/repositories";
    return $this->queryGithub($url);
  }

  /**
   * {@inheritdoc}
   */
  public function getUserRepositories($user, $page) {
    $url = "https://api.github.com/users/$user/repos?client_id=$this->clientId&client_secret=$this->clientSecret&page=$page";
    return $this->queryGithub($url);
  }

  /**
   * {@inheritdoc}
   */
  public function getOrganisationRepositories($organisation, $page) {
    $url = "https://api.github.com/orgs/$organisation/repos?client_id=$this->clientId&client_secret=$this->clientSecret&page=$page";
    return $this->queryGithub($url);
  }

}
