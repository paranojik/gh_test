<?php

namespace Drupal\gh_test\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Tests for github API.
 *
 * @group gh_test
 */
class GithubAPITest extends WebTestBase {

  /**
   * Modules to enable.
   *
   * @var array
   */
  public static $modules = array(
    'gh_test',
  );

  /**
   * The test admin user.
   *
   * @var \Drupal\User\UserInterface
   */
  protected $adminUser;

  /**
   * {@inheritdoc}
   */
  protected function setUp() {
    parent::setUp();

    $this->adminUser = $this->createUser([], NULL, TRUE);
  }

  /**
   * Test the configuration page.
   */
  public function testGithubSettingsPage() {
    $this->drupalLogin($this->adminUser);

    $this->drupalGet('admin/config/github/settings');
    $this->assertFieldByName('client_id', '');
    $this->assertFieldByName('client_secret', '');

    $edit = [
      'client_id' => 'this is my client id',
      'client_secret' => 'this is my client secret',
    ];
    $this->drupalPostForm(NULL, $edit, t('Save configuration'));

    $this->assertEqual($this->config('gh_test.config')->get('client_id'), 'this is my client id');
    $this->assertEqual($this->config('gh_test.config')->get('client_secret'), 'this is my client secret');
  }

  /**
   * Test the repositories results page.
   */
  public function testGithubResultsPage() {
    // Test the default page without parameters
    $this->drupalGet('github');
    $this->assertRaw('<strong>mojombo/grit</strong>');

    $this->assertFieldByName('search_by', 'all');
    $this->assertFieldByName('keys', '');

    // Display results for a know user.
    $this->drupalGet('github');
    $values = [
      'search_by' => 'user',
      'keys' => 'slashrsm',
    ];
    $this->drupalPostForm(NULL, $values, t('Search'));
    $this->assertRaw('<strong>slashrsm/ask_not_session</strong>');
    $this->assertFieldByName('search_by', 'user');
    $this->assertFieldByName('keys', 'slashrsm');

    // Display results for a known unexisting user.
    $this->drupalGet('github');
    $values = [
      'search_by' => 'user',
      'keys' => 'thisuserdoesnotexist',
    ];
    $this->drupalPostForm(NULL, $values, t('Search'));
    $this->assertText('Your search yielded no results.');

    // Display results for a know user.
    $this->drupalGet('github');
    $values = [
      'search_by' => 'organisation',
      'keys' => 'amazeeio',
    ];
    $this->drupalPostForm(NULL, $values, t('Search'));
    $this->assertRaw('<strong>amazeeio/cachalot</strong>');
    $this->assertFieldByName('search_by', 'organisation');
    $this->assertFieldByName('keys', 'amazeeio');
  }

}