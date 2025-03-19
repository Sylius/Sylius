@managing_inventory
Feature: Filtering inventory by code and name
    In order to quickly find tracked product variants
    As an Administrator
    I want to be able to filter tracked product variants by code or name on the list

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Iron Maiden T-Shirt" with code "iron"
        And "Iron Maiden T-Shirt" product is tracked by the inventory
        And there are 5 units of product "Iron Maiden T-Shirt" available in the inventory
        And the store has a product "RHCP T-Shirt" with code "rhcp"
        And "RHCP T-Shirt" product is tracked by the inventory
        And there are 25 units of product "RHCP T-Shirt" available in the inventory
        And I am logged in as an administrator
        And I am browsing inventory

    @ui @no-api
    Scenario: Filtering tracked product variants by code
        When I filter tracked variants with code containing "iron"
        Then I should see only one tracked variant in the list
        And I should see that the "Iron Maiden T-Shirt" variant has 5 quantity on hand

    @ui @no-api
    Scenario: Filtering tracked product variants by name
        When I filter tracked variants with name containing "RHCP"
        Then I should see only one tracked variant in the list
        And I should see that the "RHCP T-Shirt" variant has 25 quantity on hand
