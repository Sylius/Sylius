@shopping_cart
Feature: Clearing cart
    In order to quick start shopping again
    As a Customer
    I want to be able to clear my cart

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "T-Shirt banana" priced at "$12.54"
        And I am a logged in customer

    @api @ui @javascript
    Scenario: Clearing cart
        Given I added product "T-Shirt banana" to the cart
        When I check the details of my cart
        And I clear my cart
        Then my cart should be cleared
