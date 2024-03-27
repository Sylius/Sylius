@checkout
Feature: Buying product with promotion that makes the order Free
    In order to buy a product that is Free
    As a Customer
    I should be able to purchase a full discounted order

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "T-Shirt banana" priced at "$10.00"
        And the store has "DHL" shipping method with "$50.00" fee
        And there is a promotion "Holiday promotion"
        And the promotion gives "100%" discount on shipping to every order
        And the promotion gives "100%" discount to every order
        And I am a logged in customer

    @ui @api
    Scenario: Buying product with promotion that makes the order Free
        Given I have product "T-Shirt banana" in the cart
        And I am at the checkout addressing step
        And I specified the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        When I proceed with "DHL" shipping method
        Then my order total should be "$0.00"
