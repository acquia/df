@dfs_tec
Feature: DFS TEC: Team page
  In order to prove that dfs_tec was enabled correctly
  As a developer
  I need to check for the team page and team view

  Scenario: Team: Default
    Given I am at "/team"
    Then I should see the heading "Your Team"
    And The element "form.views-exposed-form" should exist
    And I should see "Bob Smith"
    And I should see "bobsmith@tec.com"
