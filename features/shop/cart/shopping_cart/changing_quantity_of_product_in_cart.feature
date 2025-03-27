@shopping_cart
Feature: Changing quantity of a product in cart
    In order to buy chosen quantity of a specific product
    As a Customer
    I want to be able to change quantity of an item in my cart

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "T-Shirt banana" priced at "$12.54"
        And I am a logged in customer

    @api @ui @javascript
    Scenario: Increasing quantity of an item in cart
        Given I added product "T-Shirt banana" to the cart
        When I see the summary of my cart
        And I change product "T-Shirt banana" quantity to 2 in my cart
        Then I should see "T-Shirt banana" with quantity 2 in my cart
        And my cart items total should be "$25.08"

    @api @ui @javascript
    Scenario: Increasing quantity of an item in cart beyond the threshold
        Given I added product "T-Shirt banana" to the cart
        When I see the summary of my cart
        And I change product "T-Shirt banana" quantity to 20000 in my cart
        Then I should be notified that the quantity of the product "T-Shirt banana" must be between 1 and 9999
        And my cart items total should be "$12.54"
