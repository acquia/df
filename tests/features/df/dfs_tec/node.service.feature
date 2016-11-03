@dfs_tec
Feature: DFS TEC: Service node
  In order to prove that dfs_tec was enabled correctly
  As a developer
  I need to be able to create nodes of the Service content type

  @api
  Scenario: Create a service node
    Given I am viewing a "service" content with the title "Investments"
    Then I should see the heading "Investments"

  @api
  Scenario: Create a service node with fields
    Given "tags" terms:
    | name     |
    | top      |
    | chef     |
    And "service" content:
    | title       | field_headline | field_subheadline | field_tags |
    | Tomfoolery  | Lorem ipsum    | Doler sit amet    | top, chef  |
    When I visit "/service/tomfoolery"
    Then I should see the heading "Tomfoolery"
    And I should see the text "Lorem ipsum"
    And I should see the text "Doler sit amet"
    And I should not see the text "top"
    And I should not see the text "chef"
