@shopping_cart
Feature: Change quantity of a product in cart
    In order to buy chosen quantity of specific product
    As a Visitor
    I want to be able to change quantity of a item in my cart

    Background:
        Given the store operates on a single channel in "France"
        And the store has a product "T-shirt banana" priced at "€12.54"
        And I add this product to the cart

    @ui
    Scenario: Increase quantity of a item in cart
        Given I see the summary of my cart
        When I change "T-shirt banana" quantity to 2
        Then I should see "T-shirt banana" with quantity 2 in my cart
