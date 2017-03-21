@df @blocks @api
Feature: Demo Framework: Blocks
  In order to demonstrate block placement and creation
  As a Solutions Architect
  I need to be able to use Content Blocks and Block Plugins in Demo Framework

  Scenario: Hero workflow
    Given I am logged in as a user with the "administrator" role
    When I go to "/block/add/hero"
    Then I fill in "edit-info-0-value" with "Homepage Hero"
    And I fill in "edit-field-first-line-0-value" with "Check out"
    And I fill in "edit-field-second-line-0-value" with "This stuff"
    And I fill in "edit-field-hero-link-0-uri" with "<front>"
    And I fill in "edit-field-hero-link-0-title" with "Click me"
    And I fill in "edit-field-icon-0-value" with "fa-ambulance"
    And I press "Save"
    Then I should see "Configure block"
    When I select "content" from "edit-region"
    And I press "Save block"
    Then I should see "The block configuration has been saved"
    When I go to "/"
    Then I should see "Homepage Hero"
    And I should see "Check out"
    And I should see "This stuff"
    And I should see "Click me"
    And I should see "" in the "i.fa-ambulance" element
    And I go to "/admin/structure/block/manage/homepagehero/delete"
    And I press "Delete"
    Then I should see "The block Homepage Hero has been deleted."

  Scenario: Map workflow
    Given I am logged in as a user with the "administrator" role
    When I go to "/block/add/map"
    Then I fill in "Block description" with "Homepage Map"
    And I fill in "Street address" with "1120 SW 5th Ave"
    And I fill in "City" with "Portland"
    And I select "Oregon" from "State"
    And I fill in "Zip code" with "97210"
    And I press "Save"
    Then I should see "Configure block"
    When I select "content" from "edit-region"
    And I press "Save block"
    Then I should see "The block configuration has been saved"
    When I go to "/"
    Then I should see "Homepage Map"
    And I should see "Address"
    And I should see "1120 SW 5th Ave"
    And I go to "/admin/structure/block/manage/homepagemap/delete"
    And I press "Delete"
    Then I should see "The block Homepage Map has been deleted."

  @javascript
  Scenario: Content Embed
    Given I am logged in as a user with the "administrator" role
    And article content:
      | title      | moderation_state |
      | My Content | published        |
    And landing_page content:
      | title  | path    | moderation_state |
      | Foobar | /foobar | draft            |
    When I go to "/foobar"
    And I embed the content "My Content" in the "middle" region
    And I click the "li[data-tab-id='save']" element
    And I wait for ajax to finish
    Then I should see "My Content"
