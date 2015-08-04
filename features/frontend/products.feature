@products
Feature: Products
    In order to know and pick the products
    As a visitor
    I want to be able to browse products

    Background:
        Given there is default currency configured
          And there are following taxonomies defined:
            | name     |
            | Category |
          And there are following users:
            | email       | password | enabled |
            | bar@foo.com | foo1     | yes     |
        And taxonomy "Category" has following taxons:
            | Clothing > T-Shirts     |
            | Clothing > PHP T-Shirts |
            | Clothing > Gloves       |
        And the following products exist:
            | name             | price | taxons       | average_rating |
            | Super T-Shirt    | 19.99 | T-Shirts     | 0              |
            | Black T-Shirt    | 18.99 | T-Shirts     | 0              |
            | Sylius Tee       | 12.99 | PHP T-Shirts | 0              |
            | Symfony T-Shirt  | 15.00 | PHP T-Shirts | 5              |
            | Doctrine T-Shirt | 15.00 | PHP T-Shirts | 0              |
        And there are following channels configured:
            | code   | name       | currencies | locales             | url          |
            | WEB-US | mystore.us | EUR, GBP   | en_US               |              |
            | WEB-EU | mystore.eu | USD        | en_GB, fr_FR, de_DE | localhost    |
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
            | title       | rating | comment               | author      | product         |
            | Lorem ipsum | 5      | Lorem ipsum dolor sit | bar@foo.com | Symfony T-Shirt |

    Scenario: Browsing products by taxon
        Given I am on the store homepage
        When I follow "T-Shirts"
        Then I should see there 1 products
        And I should see "Super T-Shirt"

    Scenario: Empty index of products
        Given there are no products
        And I am on the store homepage
        When I follow "Gloves"
        Then I should see "There are no products to display"

    Scenario: Accessing product page via title
        Given I am on the store homepage
        And I follow "PHP T-Shirts"
        When I click "Symfony T-Shirt"
        Then I should be on the product page for "Symfony T-Shirt"

    Scenario: Display only products for current channel
        Given I am on the store homepage
        Then I should see "Super T-Shirt"
        But I should not see "Black T-Shirt"

    Scenario: Receiving exception while entering page for product with empty slug
        Given I go to page for product with empty slug
         Then the response status code should be 404

    Scenario: Displaying product rating and reviews:
        Given I am on the store homepage
        And I follow "PHP T-Shirts"
        When I click "Symfony T-Shirt"
        Then I should see "Rating: 5"
        And I should see 1 review on the reviews list

    @javascript
    Scenario: Adding review as anonymous user
        Given I am on the product page for "Symfony T-Shirt"
        When I fill in "Title" with "Very good"
        And I fill in "Comment" with "Very good shirt."
        And I fill in "Author" with "guest@example.com"
        And I select the "5" radio button
        And I press "Submit"
        Then I should see "Review has been successfully created."
        And I should see 2 reviews on the reviews list

    @javascript
    Scenario: Adding review as logged in user
        Given I am logged in as "bar@foo.com"
        And I am on the product page for "Symfony T-Shirt"
        When I fill in "Title" with "Very good"
        And I fill in "Comment" with "Very good shirt."
        And I select the "5" radio button
        And I press "Submit"
        Then I should see "Review has been successfully created."
        And I should see 2 reviews on the list
