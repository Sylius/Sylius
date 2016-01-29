@products
Feature: Products
  In order to know and pick the products
  As a visitor
  I want to be able to browse products

  Background:
    Given there is default currency configured
    And there are following taxonomies defined:
      | code | name     |
      | RTX1 | Category |
    And there are following users:
      | email       | password | enabled |
      | bar@foo.com | foo1     | yes     |
    And taxonomy "Category" has following taxons:
      | Clothing[TX1] > T-Shirts[TX2]     |
      | Clothing[TX1] > PHP T-Shirts[TX3] |
      | Clothing[TX1] > Gloves[TX4]       |
    And there are following channels configured:
      | code   | name       | currencies | locales             | url       |
      | WEB-US | mystore.us | EUR, GBP   | en_US               |           |
      | WEB-EU | mystore.eu | USD        | en_GB, fr_FR, de_DE | localhost |
    And there are following attributes:
      | name           | type |
      | T-Shirt fabric | text |
    And the following products exist:
      | name             | price | taxons       | pricing calculator | calculator configuration | attributes          | average_rating |
      | Super T-Shirt    | 19.99 | T-Shirts     | channel_based      | WEB-EU:15.99             | T-Shirt fabric:Wool | 0              |
      | Black T-Shirt    | 18.99 | T-Shirts     |                    |                          |                     | 0              |
      | Sylius Tee       | 12.99 | PHP T-Shirts |                    |                          |                     | 0              |
      | Symfony T-Shirt  | 15.00 | PHP T-Shirts |                    |                          |                     | 5              |
      | Doctrine T-Shirt | 15.00 | PHP T-Shirts |                    |                          |                     | 0              |
    And channel "WEB-EU" has following configuration:
      | taxonomy |
      | Category |
    And channel "WEB-EU" has following products assigned:
      | product         |
      | Super T-Shirt   |
      | Symfony T-Shirt |
    And channel "WEB-US" has following products assigned:
      | product          |
      | Sylius Tee       |
      | Black T-Shirt    |
      | Doctrine T-Shirt |
    And there are following reviews:
      | title       | rating | comment               | author      | product         | subject type |
      | Lorem ipsum | 5      | Lorem ipsum dolor sit | bar@foo.com | Symfony T-Shirt | product      |

  Scenario: Displaying product rating and reviews:
    Given I am on the store homepage
    And I follow "PHP T-Shirts"
    When I click "Symfony T-Shirt"
    Then I should see "Rating: 5"
    And I should see 1 review on the reviews list

  @javascript
  Scenario: Adding review as logged in user
    Given I am logged in as "bar@foo.com"
    And I am on the product page for "Symfony T-Shirt"
    When I fill in "Title" with "Very good"
    And I fill in "Comment" with "Very good shirt."
    And I select the "5" radio button
    And I press "Submit"
    And I wait 3 seconds
    Then I should see "Product review has been successfully created."

  @javascript
  Scenario: Adding review as guest
    Given I am on the product page for "Symfony T-Shirt"
    When I fill in "Title" with "Very good"
    And I fill in "Comment" with "Very good shirt."
    And I fill in "Email" with "guest@example.com"
    And I select the "5" radio button
    And I press "Submit"
    And I wait 3 seconds
    Then I should see "Product review has been successfully created."

  @javascript
  Scenario: Trying to add review as guest
    Given I am on the product page for "Symfony T-Shirt"
    When I fill in "Title" with "Very good"
    And I fill in "Comment" with "Very good shirt."
    And I fill in "Email" with "bar@foo.com"
    And I select the "5" radio button
    And I press "Submit"
    And I wait 3 seconds
    Then I should see "This email is already registered. Please log in."
