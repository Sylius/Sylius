@receiving_discount
Feature: Receiving percentage discount from cart promotions on non-discounted products only
    In order to avoid receiving stacked discounts on the same product
    As a Customer
    I want to see cart promotions applied only to products that are not already discounted

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Mug" priced at "$40.00"
        And the store has a product "T-Shirt" priced at "$20.00"
        And the store has a product "Cap" priced at "$10.00"
        And there is a catalog promotion "Winter sale" that reduces price by "25%" and applies on "T-Shirt" product
        And I am a logged in customer

    @api @ui
    Scenario: Receiving product discount from cart promotions also on discounted products
        Given there is a promotion "Christmas sale" that applies to discounted products
        And this promotion gives "50%" off on every product priced between "$10.00" and "$50.00"
        And I added product "T-Shirt" to the cart
        And I added product "Mug" to the cart
        When I check the details of my cart
        Then the product "T-Shirt" should have discounted unit price "$7.50" in the cart
        And the product "Mug" should have discounted unit price "$20.00" in the cart
        And my cart total should be "$27.50"

    @api @ui
    Scenario: Receiving product discount from cart promotions only on non discounted products
        Given there is a promotion "Christmas sale" that does not apply to discounted products
        And this promotion gives "50%" off on every product priced between "$10.00" and "$50.00"
        And I added product "T-Shirt" to the cart
        And I added product "Mug" to the cart
        When I check the details of my cart
        Then the product "T-Shirt" should have discounted unit price "$15.00" in the cart
        And the product "Mug" should have discounted unit price "$20.00" in the cart
        And the cart total should be "$35.00"

    @api @no-ui
    Scenario: Receiving order discount from cart promotions distributed only on non discounted products
        Given there is a promotion "Christmas sale" that does not apply to discounted products
        And this promotion gives "50%" discount to every order
        And I added product "T-Shirt" to the cart
        And I added product "Mug" to the cart
        And I added product "Cap" to the cart
        When I check the details of my cart
        Then the product "T-Shirt" should have discounted unit price "$15.00" in the cart
        And the product "Mug" should have total price "$20.00" in the cart
        And the product "Cap" should have total price "$5.00" in the cart
        And the cart total should be "$40.00"
