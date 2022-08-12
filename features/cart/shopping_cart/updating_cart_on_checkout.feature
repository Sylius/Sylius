@shopping_cart
Feature:
    In order to avoid taking additional steps before accessing checkout
    As a Visitor
    I want my cart to be updated on checkout

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "T-Shirt banana" priced at "$12.54"
        And I added this product to the cart

    @ui @no-api
    Scenario: Updating the cart on checkout
        Given I am on the summary of my cart page
        When I specify product "T-Shirt banana" quantity to 2
        And I proceed to the checkout
        Then I should be on the checkout addressing page
        And the quantity of "T-Shirt banana" should be 2
