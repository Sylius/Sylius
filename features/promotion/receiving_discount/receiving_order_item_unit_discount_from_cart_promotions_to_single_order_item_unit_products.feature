@receiving_discount
Feature: Receiving percentage discount from cart promotions to products configured as single order item unit
    In order not to combine cart and catalog promotions
    As a Store Owner
    I want to apply discount only on products that are configured to as single order item unit

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "T-Shirt" configured as single order item unit priced at "$20.00"

    @ui @api
    Scenario: Receiving product discount from cart promotions in products configured as single order item unit
        Given there is a promotion "Christmas sale" that applies to discounted products
        And this promotion gives "50%" off on every product priced between "$10.00" and "$50.00"
        When I add 10 products "T-Shirt" to the cart
        Then the product "T-Shirt" should have discounted unit price "$10.00" in the cart
        And my cart total should be "$100.00"
