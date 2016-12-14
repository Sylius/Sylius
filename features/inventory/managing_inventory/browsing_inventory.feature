@managing_inventory
Feature: Browsing inventory
    In order to see all tracked product variants
    As an Administrator
    I want to be able to browse tracked product variants

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a "Wyborowa Vodka" configurable product
        And the product "Wyborowa Vodka" has "Wyborowa Vodka Exquisite" variant priced at "$40.00"
        And the store has a product "Iron Maiden T-Shirt"
        And "Iron Maiden T-Shirt" product is tracked by the inventory
        And there are 5 units of product "Iron Maiden T-Shirt" available in the inventory
        And I am logged in as an administrator

    @ui
    Scenario: Browsing only tracked product variants in the store
        When I want to browse inventory
        Then I should see only one tracked variant in the list

    @ui
    Scenario: Being informed about on hand quantity of a product variant
        When I want to browse inventory
        Then I should see that the "Iron Maiden T-Shirt" variant has 5 quantity on hand
