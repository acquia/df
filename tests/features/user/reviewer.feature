@df @user @api
Feature: Content reviewer user
  As a content reviewer, I need to be able to review and moderate content.

  @71c1b000
  Scenario: Content reviewers should not be able to access administration pages.
    Given I am logged in as a user with the "reviewer" role
    When I go to "/admin"
    Then the response status code should be 403

  @1c1c24d0
  Scenario: Content reviewers should be able to view lists of site content
    Given I am logged in as a user with the "reviewer" role
    When I visit "/admin/content"
    And I should see the page title "Content"
    Then the element ".fixed-action-btn.horizontal" should exist

  @2a6cd1e6
  Scenario: Content reviewers should not be able to view lists of site media
    Given I am logged in as a user with the "reviewer" role
    When I go to "/admin/content/media"
    Then the response status code should be 403
