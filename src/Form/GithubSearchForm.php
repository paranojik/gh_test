<?php

namespace Drupal\gh_test\Form;

use Drupal\Core\Form\FormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Search form for Github repositories.
 */
class GithubSearchForm extends FormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'gh_test_github_search';
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    $request = $this->getRequest();

    $form['search_by'] = [
      '#type' => 'select',
      '#title' => $this->t('Search by'),
      '#options' => [
        'all' => $this->t('All public repositories'),
        'organisation' => $this->t('Organisation public repositories'),
        'user' => $this->t('User public repositories'),
      ],
      '#default_value' => $request->query->has('search_by') ? $request->query->get('search_by') : 'all',
      '#required' => TRUE,
    ];

    $form['keys'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Github search key'),
      '#default_value' => $request->query->has('keys') ? $request->query->get('keys') : '',
      '#states' => array(
        'invisible' => array(
          ':input[name="search_by"]' => array('value' => 'all'),
        ),
      ),
    ];

    $form['basic']['submit'] = array(
      '#type' => 'submit',
      '#value' => $this->t('Search'),
    );

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    // Redirect back to content page with the search parameters in the
    // query string.
    $query = [
      'search_by' => $form_state->getValue('search_by'),
      'keys' => trim($form_state->getValue('keys')),
    ];
    $route = 'gh_test.content';
    $form_state->setRedirect(
      $route,
      array(),
      array('query' => $query)
    );
  }

}
