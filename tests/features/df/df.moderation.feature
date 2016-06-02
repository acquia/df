@df @workflow
Feature: Demo Framework: Moderation
  In order to demonstrate a content management workflow
  As a Solutions Architect
  I need to be able to use Workbench in Demo Framework
 
  @api @javascript
  Scenario: Article workflow
    Given I am logged in as the user "KatieAuthor"
      When I go to "/node/add/article"
        Then I fill in "edit-title-0-value" with "A rant about sports"
          And I maximize the browser window
          And I fill in "body" with "A really long diatribe regarding why St. Louis needed to get rid of Pujols so that they could rebuild for the future."
          And I press "Save and Create New Draft"
          Then I should see "A rant about sports"
          And I should see "Pujols"
          And I should see "future."
          And I should see "Change Layout"
          And I should see "Manage Content"
          And I should see "Edit"
      When I click on the element with xpath "//a[@id='moderate-mmenu-link']"
        Then I should see "Moderate Article"
          And I should see "Currently Viewing: Draft"
          And I should see "View"
          And I should see "Edit draft"
          And I should see "Delete"
          And I should see "Translate"
          And I should see "Status"
          And I should see "Draft"
      When I fill in "edit-revision-log" with "Check this out, Paul."
        Then I should see "The moderation state has been updated."
