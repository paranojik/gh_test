<?php

namespace Drupal\gh_test;

/**
 * Interface GithubAPIInterface.
 *
 * @package Drupal\gh_test
 */
/**
 * Interface GithubAPIInterface.
 *
 * @package Drupal\gh_test
 */
interface GithubAPIInterface {

  /**
   * Retrieves a list of all public github repositories.
   *
   * @return array
   *   List of rew github repository objects.
   */
  public function getRepositories();

  /**
   * Retrieves a list of user public github repositories.
   *
   * @param string $user
   *   Github username.
   * @param int $page
   *   Result page number.
   *
   * @return array
   *   List of rew github repository objects.
   */
  public function getUserRepositories($user, $page);

  /**
   * Retrieves a list of company public github repositories.
   *
   * @param string $organisation
   *   Organisation name.
   * @param int $page
   *   Result page number.
   *
   * @return array
   *   List of rew github repository objects.
   */
  public function getOrganisationRepositories($organisation, $page);

}
