@checkout
Feature: Seeing shipping discount on order summary
    In order to be sure that the shipping discount was applied to my order
    As a Customer
    I want to be able to see shipping promotion on the order summary

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$19.99"
        And the store has "DHL" shipping method with "$50.00" fee
        And there is a promotion "Holiday promotion"
        And the promotion gives "10%" discount on shipping to every order
        And the store allows paying offline
        And I am a logged in customer

    @ui
    Scenario: Seeing order shipping discount on the order summary page
        Given I have product "PHP T-Shirt" in the cart
        And I am at the checkout addressing step
        When I specified the shipping address as "Ankh Morpork", "Frost Alley", "90210", "United States" for "Jon Snow"
        And I proceed with "DHL" shipping method and "Offline" payment
        Then I should be on the checkout summary step
        And "Holiday promotion" should be applied to my order shipping
        And this promotion should give "-$5.00" discount
