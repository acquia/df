@df @user @api
Feature: Content creator user
  As a content creator, I need to be able to create and edit content.

  @c1a9aca4
  Scenario: Content creators should not be able to access administration pages.
    Given I am logged in as a user with the "creator" role
    When I go to "/admin"
    Then the response status code should be 403

  @3e83879e
  Scenario: Content creators should be able to view lists of site content
    Given I am logged in as a user with the "creator" role
    When I visit "/admin/content"
    And I should see the page title "Content"
    Then the element ".fixed-action-btn.horizontal" should exist

  @360d4f13
  Scenario: Content creators should be able to view lists of site media
    Given I am logged in as a user with the "creator" role
    When I visit "/admin/content/media"
    And I should see the page title "Media"
    Then the element ".fixed-action-btn.horizontal" should exist
    