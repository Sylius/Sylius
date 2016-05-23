@legacy @product
Feature: Products filter
    In order to easily find products
    As a store owner
    I want to be able to filter list by name

    Background:
        Given store has default configuration
        And the following products exist:
            | name          | price | code |
            | Super T-Shirt | 19.99 | 123  |
            | Black T-Shirt | 19.99 | 321  |
            | Sticker       | 10.00 | 555  |
            | Orange        | 10.00 | 124  |
        And I am logged in as administrator

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
        When I fill in "Code" with "555-VARIANT"
        And I press "Filter"
        Then I should be on the product index page
        And I should see 1 product in the list
        And I should see "Sticker"
        But I should not see "T-Shirt"
