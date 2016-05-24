@shopping_cart
Feature: Removing cart item from cart
    In order to delete some unnecessary cart items
    As a Visitor
    I want to be able to remove cart item

    Background:
        Given the store operates on a single channel in "France"
        And the store has a product "T-shirt banana" priced at "€12.54"
        And I add this product to the cart

    @ui
    Scenario: Removing cart item
        Given I see the summary of my cart
        When I remove product "T-shirt banana" from the cart
        Then my cart should be empty
