@df @blocks
Feature: Demo Framework: Blocks
  In order to demonstrate custom block creation
  As a Solutions Architect
  I need to be able to use Content Block Types in Demo Framework

  @api
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

  @api
  Scenario: Map workflow
    Given I am logged in as a user with the "administrator" role
    When I go to "/block/add/map"
    Then I fill in "edit-info-0-value" with "Homepage Map"
    And I fill in "edit-field-map-address-0-address-line1" with "1120 SW 5th Ave"
    And I fill in "edit-field-map-address-0-locality" with "Portland"
    And I select "US-OR" from "edit-field-map-address-0-administrative-area"
    And I fill in "edit-field-map-address-0-postal-code" with "97210"
    And I press "Save"
    Then I should see "Configure block"
    When I select "content" from "edit-region"
    And I press "Save block"
    Then I should see "The block configuration has been saved"
    When I go to "/"
    Then I should see "Homepage Map"
    And I should see "Address"
    And I should see "1120 SW 5th Ave"
