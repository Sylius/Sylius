@checkout
Feature: Adding product with promotion that makes the order free
    I want to buy a product with a promotion that gives me free shipping and free order

    Background:
        Given the store operates on a single channel in "United States"
        And the store has "DHL" shipping method with "$50.00" fee
        And there is a promotion "Holiday promotion"
        And the promotion gives "100%" discount on shipping to every order
        And the promotion gives "100%" discount to every order
        And the store allows paying offline
        And I am a logged in customer

    @ui
    Scenario: Adding a simple product to the cart
        Given the store has a product "T-shirt banana" priced at "$10.00"
        When I add this product to the cart
        And I am at the checkout addressing step
        When I specified the billing address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I proceed with "DHL" shipping method and "Offline" payment
        Then I should be on the checkout summary step
        And "Holiday promotion" should be applied to my order shipping
        Then my order total should be "$0.00"
        And I confirm my order
