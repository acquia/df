@df @core @api
Feature: User roles and related config

  @394cee8a
  Scenario: Administrator Role select list should be present in Account Settings
    Given I am logged in as a user with the administrator role
    When I visit "/admin/config/people/accounts"
    Then the "Administrator role" field should contain "Administrator"
