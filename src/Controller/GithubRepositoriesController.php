<?php

namespace Drupal\gh_test\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\gh_test\GithubAPIInterface;
use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Github repositories page controller.
 */
class GithubRepositoriesController extends ControllerBase implements ContainerInjectionInterface {

  /**
   * The Github API service.
   *
   * @var \Drupal\gh_test\GithubAPIInterface
   */
  protected $githubApi;

  /**
   * Constructs a NodeController object.
   *
   * @param \Drupal\Core\Datetime\GithubAPIInterface $githubApi
   *   The date formatter service.
   */
  public function __construct(GithubAPIInterface $githubApi) {
    $this->githubApi = $githubApi;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('gh_test.github_api')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function content(Request $request) {
    $build = array();

    // Inject the search form.
    $build['search_form'] = $this->formBuilder()->getForm('Drupal\gh_test\Form\GithubSearchForm');

    if ($request->query->has('keys')) {
      $keys = trim($request->query->get('keys'));
    }

    if ($request->query->has('search_by')) {
      $search_by = trim($request->query->get('search_by'));
    }
    else {
      $search_by = 'all';
    }

    $page = 0;
    if ($request->query->has('page')) {
      $page = trim($request->query->get('page'));
    }

    $results = [];

    switch ($search_by) {
      case 'user':
        // Github starts counting pages with 1.
        $repositories = $this->githubApi->getUserRepositories($keys, $page + 1);
        break;

      case 'organisation':
        // Github starts counting pages with 1.
        $repositories = $this->githubApi->getOrganisationRepositories($keys, $page + 1);
        break;

      default:
        // Retrieve the public repositories list.
        $repositories = $this->githubApi->getRepositories();
        break;
    }

    if (!empty($repositories)) {
      $total = count($repositories);
      // If we got 30 results, the total is probably higher than that.
      // The github API returns 30 items by default, when the endpoint
      // supports pagination.
      if ($total == 30) {
        $total = ($page + 1) * 30 + 1;
        pager_default_initialize($total, 30);
      }

      foreach ($repositories as $delta => $repository) {
        $results[$delta] = [
          '#theme' => 'gh_test_repository',
          '#name' => $repository->full_name,
          '#url' => $repository->html_url,
          '#description' => $repository->description,
          '#attributes' => [
            'class' => ['github-repository'],
            'lang' => 'en',
          ],
        ];
      }
    }

    $build['#cache'] = [
      'contexts' => [
        'url.query_args',
      ]
    ];

    $build['github_results'] = array(
      '#theme' => array('item_list__github_results'),
      '#items' => $results,
      '#empty' => array(
        '#markup' => '<h3>' . $this->t('Your search yielded no results.') . '</h3>',
      ),
      '#list_type' => 'ul',
    );

    $build['pager'] = array(
      '#type' => 'pager',
    );

    return $build;
  }

}
