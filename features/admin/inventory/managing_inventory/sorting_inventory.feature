@managing_inventory
Feature: Sorting inventory
    In order to change the order by which tracked product variants are displayed
    As an Administrator
    I want to be able to sort tracked product variants on the list

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

    @ui @no-api
    Scenario: Displaying tracked product variants sorted by name in ascending order by default
        Then I should see 2 tracked variants in the list
        And the first variant on the list should have name "Iron Maiden T-Shirt"
        And the last variant on the list should have name "RHCP T-Shirt"

    @ui @no-api
    Scenario: Sorting tracked product variants descending by name
        When I sort the tracked variants descending by name
        Then I should see 2 tracked variants in the list
        And the first variant on the list should have name "RHCP T-Shirt"
        And the last variant on the list should have name "Iron Maiden T-Shirt"

    @ui @no-api
    Scenario: Sorting tracked product variants ascending by code
        When I sort the tracked variants ascending by code
        Then I should see 2 tracked variants in the list
        And the first variant on the list should have code "IRON_MAIDEN_T_SHIRT"
        And the last variant on the list should have code "RHCP_T_SHIRT"

    @ui @no-api
    Scenario: Sorting tracked product variants descending by code
        When I sort the tracked variants descending by code
        Then I should see 2 tracked variants in the list
        And the first variant on the list should have code "RHCP_T_SHIRT"
        And the last variant on the list should have code "IRON_MAIDEN_T_SHIRT"
