@df @workflow
Feature: Demo Framework: Moderation
  In order to demonstrate a content management workflow
  As a Solutions Architect
  I need to be able to use Workbench in Demo Framework

  @api
  Scenario: Article workflow
    Given I am logged in as a user with the "creator" role
    When I go to "/node/add/article"
    Then I fill in "edit-title-0-value" with "A rant about sports"
    And I fill in "edit-body-0-value" with "A really long diatribe regarding why St. Louis needed to get rid of Pujols so that they could rebuild for the future."
    And I press "Save"
    Then I should see "A rant about sports"
    And I should see "Pujols"
    And I should see "future."
