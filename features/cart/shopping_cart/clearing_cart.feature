@shopping_cart
Feature: Clearing cart
    In order to quick start shopping again
    As a Visitor
    I want to be able to clear my cart

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "T-Shirt banana" priced at "$12.54"
        And I added this product to the cart

    @ui @api
    Scenario: Clearing cart
        Given I see the summary of my cart
        When I clear my cart
        Then my cart should be cleared
