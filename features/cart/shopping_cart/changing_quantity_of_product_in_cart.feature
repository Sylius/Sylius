@shopping_cart
Feature: Changing quantity of a product in cart
    In order to buy chosen quantity of a specific product
    As a Visitor
    I want to be able to change quantity of an item in my cart

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "T-shirt banana" priced at "$12.54"
        And I add this product to the cart

    @ui
    Scenario: Increasing quantity of an item in cart
        Given I see the summary of my cart
        When I change "T-shirt banana" quantity to 2
        Then I should see "T-shirt banana" with quantity 2 in my cart

    @ui
    Scenario: Increasing quantity of an item in cart beyond the threshold
        Given I see the summary of my cart
        When I change "T-shirt banana" quantity to 20000
        Then I should see "T-shirt banana" with quantity 9999 in my cart
