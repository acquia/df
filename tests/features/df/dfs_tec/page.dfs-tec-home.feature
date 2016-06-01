@dfs_tec
Feature: DFS TEC: Homepage
  In order to prove that dfs_tec was enabled correctly
  As a developer
  I need to check for elements on the front page

  @api
  Scenario: Block: Slideshow
    Given I am on the homepage
    Then I should see "Insight that makes sense of it all" in the "top" region
    And I should see "Strong providers in innovation." in the "top" region
    And I should see "The perspective that affects your bottom line." in the "top" region

  @api
  Scenario: Block: Services Promo
    Given I am on the homepage
    Then I should see "Bayside Consulting is your global business consulting partner." in the "primary" region
    And I should see "Digital Consulting" in the "primary" region
    And I should see "Business Transformation" in the "primary" region
    And I should see "Research Analysis" in the "primary" region

  Scenario: Block: Recent Article
    Given I am on the homepage
    Then I should see "Latest articles" in the "secondary" region
    And I should see "Planning for success" in the "secondary" region
    And I should see "The Lost Art of Creativity" in the "secondary" region
    And I should see "Scalable Personalization" in the "secondary" region

  Scenario: Block: Footer contact
    Given I am on the homepage
    Then I should see "Address" in the "footer" region
    And I should see "Press/PR" in the "footer" region
    And I should see "Social" in the "footer" region
