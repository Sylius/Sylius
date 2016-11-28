@receiving_discount
Feature: Receiving fixed discount dependent on channel on cart
    In order to pay proper amount while buying promoted goods in different channels
    As a Visitor
    I want to have promotions applied to my cart

    Background:
        Given the store operates on a channel named "Web-US" in "USD" currency
        And the store operates on another channel named "Web-GB" in "GBP" currency
        And the store has a product "PHP T-Shirt" priced at "$100.00" in "Web-US" channel
        And this product is also priced at "£80.00" in "Web-GB" channel
        And there is a promotion "Holiday promotion"
        And this promotion gives "$10.00" discount to every order in the "Web-US" channel and "£12.00" discount to every order in the "Web-GB" channel

    @ui
    Scenario: Receiving fixed discount in proper currency for channel
        When I change my current channel to "Web-US"
        And I add product "PHP T-Shirt" to the cart
        Then my cart total should be "$90.00"
        And my discount should be "-$10.00"

    @ui
    Scenario: Receiving fixed discount in proper currency after channel change
        When I change my current channel to "Web-GB"
        And I add product "PHP T-Shirt" to the cart
        Then my cart total should be "£68.00"
        And my discount should be "-£12.00"
