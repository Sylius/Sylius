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

    @api @ui
    Scenario: Placing an order with products that have sufficient quantity
        Given I added 3 products "Iron Maiden T-Shirt" to the cart
        And I changed product "Iron Maiden T-Shirt" quantity to 5 in my cart
        When I check the details of my cart
        And I should see "Iron Maiden T-Shirt" with quantity 5 in my cart
