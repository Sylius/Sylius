@managing_inventory
Feature: Filtering inventory by product
    In order to see only tracked product variants of a specific product
    As an Administrator
    I want to be able to filter tracked product variants by product on the list

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Iron Maiden T-Shirt"
        And "Iron Maiden T-Shirt" product is tracked by the inventory
        And there are 5 units of product "Iron Maiden T-Shirt" available in the inventory
        And the store has a product "RHCP T-Shirt"
        And "RHCP T-Shirt" product is tracked by the inventory
        And there are 25 units of product "RHCP T-Shirt" available in the inventory
        And I am logged in as an administrator
        And I am browsing inventory

    @ui @mink:chromedriver @no-api
    Scenario: Filtering tracked product variants by product
        When I filter tracked variants by "RHCP T-Shirt" product
        Then I should see only one tracked variant in the list
        And I should see that the "RHCP T-Shirt" variant has 25 quantity on hand
