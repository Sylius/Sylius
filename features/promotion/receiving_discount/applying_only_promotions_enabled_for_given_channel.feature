@receiving_discount
Feature: Applying only promotions enabled for given channel
    In order to place a valid order
    As a Visitor
    I want to have only available promotions applied to my cart

    Background:
        Given the store operates on a single channel in the "United States" named "Web"
        And the store has a product "PHP T-Shirt" priced at "$100.00"
        And there is a promotion "Holiday promotion"
        And it gives "$10.00" discount to every order

    @ui
    Scenario: Receiving fixed discount for my cart
        When I add product "PHP T-Shirt" to the cart
        Then my cart total should be "$90.00"
        And my discount should be "-$10.00"

    @ui
    Scenario: Not receiving discount when promotion is disabled for current channel
        Given the promotion was disabled for the channel "Web"
        When I add product "PHP T-Shirt" to the cart
        Then my cart total should be "$100.00"
        And there should be no discount
