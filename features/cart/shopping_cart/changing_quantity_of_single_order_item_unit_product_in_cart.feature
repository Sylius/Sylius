@shopping_cart
Feature: Changing quantity of a product configured as single order item unit in cart
    In order to buy chosen quantity of a specific product which is configured to be single order item unit
    As a Visitor
    I want to be able to change quantity of an item in my cart

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "T-Shirt grape" configured as single order item unit priced at "$25.12"
        And I add this product to the cart

    @ui @api
    Scenario: Increasing quantity of an item in cart
        Given I see the summary of my cart
        When I change product "T-Shirt grape" quantity to 17 in my cart
        Then there should be one item in my cart
        And I should see "T-Shirt grape" with quantity 17 in my cart

    @ui @api
    Scenario: Increasing quantity of an item in cart beyond the threshold
        Given I see the summary of my cart
        When I change product "T-Shirt grape" quantity to 20000 in my cart
        Then there should be one item in my cart
        And I should see "T-Shirt grape" with quantity 9999 in my cart
