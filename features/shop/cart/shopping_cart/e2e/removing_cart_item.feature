@shopping_cart
Feature: Removing cart item from cart
    In order to delete some unnecessary cart items
    As a Visitor
    I want to be able to remove cart item

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "T-Shirt banana" priced at "$12.54"

    @api @ui @mink:chromedriver
    Scenario: Removing cart item
        Given I added product "T-Shirt banana" to the cart
        When I see the summary of my cart
        And I remove product "T-Shirt banana" from the cart
        Then my cart should be empty
        And my cart's total should be "$0.00"
