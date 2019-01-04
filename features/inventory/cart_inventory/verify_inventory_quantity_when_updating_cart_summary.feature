@cart_inventory
Feature: Verifying inventory quantity on cart summary
    In order to not be able to add more items than available
    As a Customer
    I want to be notified that requested item quantity cannot be handled

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Iron Maiden T-Shirt" priced at "€12.54"
        And this product is tracked by the inventory
        And there are 5 units of product "Iron Maiden T-Shirt" available in the inventory

    @ui
    Scenario: Being unable to save a cart with product that is out of stock
        Given I have added 3 products "Iron Maiden T-Shirt" in the cart
        When I change "Iron Maiden T-Shirt" quantity to 6
        And I update my cart
        Then I should be notified that this product cannot be updated

    @ui
    Scenario: Placing an order with products that have sufficient quantity
        Given I have added 3 products "Iron Maiden T-Shirt" in the cart
        When I change "Iron Maiden T-Shirt" quantity to 5
        And I update my cart
        Then I should not be notified that this product cannot be updated
