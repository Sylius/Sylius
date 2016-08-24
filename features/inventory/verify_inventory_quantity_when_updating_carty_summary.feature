@inventory
Feature: Verifying inventory quantity on cart summary
    In order to not be able to add more items than available
    As a Customer
    I want to be notified that requested item quantity cannot be handle

    Background:
        Given the store operates on a single channel in "France"
        And the store has a product "Iron Maiden T-Shirt" priced at "€12.54"
        And there are 5 items of product "Iron Maiden T-Shirt" available in the inventory
        And the store ships everywhere for free
        And the store allows paying offline

    @todo
    Scenario: Product quantity validation
        Given I have 3 products "Iron Maiden T-Shirt" in the cart
        When I change "Iron Maiden T-Shirt" quantity to 6
        Then I should be notified that requested item quantity cannot be handle
