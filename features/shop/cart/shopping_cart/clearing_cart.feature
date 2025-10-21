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

    @api @ui @javascript
    Scenario: Clearing cart after adding an address in checkout
        Given I added product "T-Shirt banana" to the cart
        And I have specified the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        When I check the details of my cart
        And I clear my cart
        Then my cart should be cleared
