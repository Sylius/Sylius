@managing_inventory
Feature: Filtering inventory by a code
    In order to filter tracked product variants by a name
    As an Administrator
    I want to be able to filter tracked product variants on the list

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Iron Maiden T-Shirt" with code "iron"
        And "Iron Maiden T-Shirt" product is tracked by the inventory
        And there are 5 units of product "Iron Maiden T-Shirt" available in the inventory
        And the store has a product "RHCP T-Shirt" with code "rhcp"
        And "RHCP T-Shirt" product is tracked by the inventory
        And there are 25 units of product "RHCP T-Shirt" available in the inventory
        And I am logged in as an administrator

    @ui @todo
    Scenario: Filtering tracked product variants by a name
        When I want to browse inventory
        And I choose "Contains" as a filter code type
        And I specify filter code as "iron"
        And I filter
        Then I should see a single tracked variant in the list
        And I should see that the "Iron Maiden T-Shirt" variant has 5 quantity on hand
