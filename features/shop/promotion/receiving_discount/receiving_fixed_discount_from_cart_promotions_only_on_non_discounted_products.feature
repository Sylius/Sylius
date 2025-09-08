@receiving_discount
Feature: Receiving fixed discount from cart promotions only on non discounted products
    In order to avoid combining cart promotions with catalog promotions,
    As a Customer
    I want to receive a fixed discount on my order only for those products that are not already discounted by a catalog promotion

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
        And this promotion gives "$10.00" off on every product priced between "$10.00" and "$50.00"
        And I added product "T-Shirt" to the cart
        And I added product "Mug" to the cart
        When I check the details of my cart
        Then the product "T-Shirt" should have discounted unit price "$5.00" in the cart
        And the product "Mug" should have discounted unit price "$30.00" in the cart
        And my cart total should be "$35.00"

    @api @ui
    Scenario: Receiving product discount from cart promotions only on non discounted products
        Given there is a promotion "Christmas sale" that does not apply to discounted products
        And this promotion gives "$10.00" off on every product priced between "$10.00" and "$50.00"
        When I added product "T-Shirt" to the cart
        And I added product "Mug" to the cart
        When I check the details of my cart
        Then the product "T-Shirt" should have discounted unit price "$15.00" in the cart
        And the product "Mug" should have discounted unit price "$30.00" in the cart
        And the cart total should be "$45.00"

    @api @no-ui
    Scenario: Receiving order discount from cart promotions distributed only on non discounted products
        Given there is a promotion "Christmas sale" that does not apply to discounted products
        And this promotion gives "$10.00" discount to every order
        When I added product "T-Shirt" to the cart
        And I added product "Mug" to the cart
        And I added product "Cap" to the cart
        When I check the details of my cart
        Then the product "T-Shirt" should have discounted unit price "$15.00" in the cart
        And the product "Mug" should have total price "$32.00" in the cart
        And the product "Cap" should have total price "$8.00" in the cart
        And the cart total should be "$55.00"
