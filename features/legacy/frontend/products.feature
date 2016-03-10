@legacy @product
Feature: Products
    In order to know and pick the products
    As a visitor
    I want to be able to browse products

    Background:
        Given there is default currency configured
        And there are following taxons defined:
            | code | name     |
            | RTX1 | Category |
        And taxon "Category" has following children:
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
            | name             | price | taxons       | pricing calculator | calculator configuration | attributes          |
            | Super T-Shirt    | 19.99 | T-Shirts     | channel_based      | WEB-EU:15.99             | T-Shirt fabric:Wool |
            | Black T-Shirt    | 18.99 | T-Shirts     |                    |                          |                     |
            | Sylius Tee       | 12.99 | PHP T-Shirts |                    |                          |                     |
            | Symfony T-Shirt  | 15.00 | PHP T-Shirts |                    |                          |                     |
            | Doctrine T-Shirt | 15.00 | PHP T-Shirts |                    |                          |                     |
        And channel "WEB-EU" has following configuration:
            | taxon    |
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

    Scenario: Display proper product price for specific channel
        Given I am on the store homepage
        Then I should see "Super T-shirt"
        And I should see "€15.99"

    Scenario: Display product attributes
        Given I am on the product page for "Super T-shirt"
        Then I should see "Wool"

    Scenario: Receiving exception while entering page for product with empty slug
        Given I go to page for product with empty slug
        Then the response status code should be 404
