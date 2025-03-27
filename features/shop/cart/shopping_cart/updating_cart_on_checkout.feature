@shopping_cart
Feature:
    In order to avoid taking additional steps before accessing checkout
    As a Customer
    I want my cart to be updated on checkout

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "T-Shirt banana" priced at "$12.54"
        And I am a logged in customer

    @no-api @ui @mink:chromedriver
    Scenario: Updating the cart on checkout
        Given I added product "T-Shirt banana" to the cart
        When I check details of my cart
        And I change product "T-Shirt banana" quantity to 2
        And I proceed to the checkout
        Then I should be on the checkout addressing page
        And the quantity of "T-Shirt banana" should be 2
