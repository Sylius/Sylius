@products
Feature: Products filter
    In order to easily find products
    As a store owner
    I want to be able to filter list by name

    Background:
        Given there is default currency configured
        And I am logged in as administrator
        And the following products exist:
            | name          | price | sku |
            | Super T-Shirt | 19.99 | 123 |
            | Black T-Shirt | 19.99 | 321 |
            | Mug           | 5.99  | 136 |
            | Sticker       | 10.00 | 555 |
            | Banana        | 10.00 | 999 |
            | Orange        | 10.00 | 124 |

    Scenario: Filtering products by name
        Given I am on the product index page
        When I fill in "Name" with "T-Shirt"
        And I press "Filter"
        Then I should be on the product index page
        And I should see 2 products in the list
        And I should not see "Orange"
        But I should see "Black T-Shirt"

    Scenario: Filtering products by SKU
        Given I am on the product index page
        When I fill in "SKU" with "123"
        And I press "Filter"
        Then I should be on the product index page
        And I should see 1 product in the list
        And I should see "Sticker"
        But I should not see "T-Shirt"
