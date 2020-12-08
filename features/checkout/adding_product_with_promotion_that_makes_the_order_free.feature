@checkout
Feature: Buying product with promotion that makes the order free
    In order to buy a product that is free
    As a Customer
    I should be able to purchase a full discounted order

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "T-shirt banana" priced at "$10.00"
        And the store has "DHL" shipping method with "$50.00" fee
        And there is a promotion "Holiday promotion"
        And the promotion gives "100%" discount on shipping to every order
        And the promotion gives "100%" discount to every order
        And the store allows paying offline
        And I am a logged in customer

    @ui
    Scenario: Buying product with promotion that makes the order free
        When I add product "T-shirt banana" to the cart
        And I go to the checkout addressing step
        And I specified the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I proceed with "DHL" shipping method
        Then I should be on the checkout summary step
        And "Holiday promotion" should be applied to my order shipping
        And my order total should be "$0.00"
        And I confirm my order
