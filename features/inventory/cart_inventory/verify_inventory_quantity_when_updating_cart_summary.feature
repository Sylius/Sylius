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
        And the store has a product "Black Dress" priced at "€50.20"
        And this product is tracked by the inventory
        And there are 10 units of product "Black Dress" available in the inventory

    @ui @api
    Scenario: Being unable to save a cart with product that is out of stock
        Given I have added 3 products "Iron Maiden T-Shirt" to the cart
        When I change product "Iron Maiden T-Shirt" quantity to 6 in my cart
        And I update my cart
        Then I should be notified that this product has insufficient stock

    @ui @no-api
    Scenario: Preventing the cart recalculation when the form has errors
        Given I have added 3 products "Iron Maiden T-Shirt" to the cart
        And I have added 1 product "Black Dress" to the cart
        When I change product "Iron Maiden T-Shirt" quantity to 4 in my cart
        And I change product "Black Dress" quantity to 11 in my cart
        Then I should be notified that this product has insufficient stock
        And I should see "Iron Maiden T-Shirt" with quantity 4 in my cart
        And I should see "Black Dress" with quantity 11 in my cart
        And my cart's total should be "$100.36"

    @ui @api
    Scenario: Placing an order with products that have sufficient quantity
        Given I have added 3 products "Iron Maiden T-Shirt" in the cart
        When I change product "Iron Maiden T-Shirt" quantity to 5 in my cart
        And I update my cart
        Then I should not be notified that this product cannot be updated
