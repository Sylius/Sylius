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
        And I am a logged in customer

    @api @ui @mink:chromedriver
    Scenario: Being unable to save a cart with product that is out of stock
        Given I added 3 products "Iron Maiden T-Shirt" to the cart
        When I change product "Iron Maiden T-Shirt" quantity to 6 in my cart
        Then I should be notified that this product has insufficient stock
