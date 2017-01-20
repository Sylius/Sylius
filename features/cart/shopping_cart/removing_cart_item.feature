@shopping_cart
Feature: Removing cart item from cart
    In order to delete some unnecessary cart items
    As a Visitor
    I want to be able to remove cart item

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "T-shirt banana" priced at "$12.54"
        And I added product "T-shirt banana" to the cart

    @ui @javascript
    Scenario: Removing cart item
        When I see the summary of my cart
        And I remove product "T-shirt banana" from the cart
        Then my cart should be empty
        And my cart's total should be "$0.00"

    @ui @javascript
    Scenario: Removing cart item when the store has defined default shipping method
        Given the store has "UPS" shipping method with "$20.00" fee
        When I remove product "T-shirt banana" from the cart
        Then my cart should be empty
        And my cart's total should be "$0.00"

    @ui @javascript
    Scenario: Checking cart's total after removing one item
        Given the store has a product "T-shirt strawberry" priced at "$17.22"
        And I add this product to the cart
        When I remove product "T-shirt banana" from the cart
        Then my cart's total should be "$17.22"
