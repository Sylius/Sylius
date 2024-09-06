@shopping_cart
Feature: Viewing a cart summary in many channels
    In order to see details about my order in selected channel
    As a Visitor
    I want to see different carts in different channels

    Background:
        Given the store operates on another channel named "France"
        And there is product "Banana" available in this channel
        And the store operates on a channel named "Poland" in "PLN" currency
        And there is product "Onion" available in this channel

    @ui
    Scenario: Viewing information about empty cart after channel switching
        Given I changed my current channel to "Poland"
        And I added product "Onion" to the cart
        When I change my current channel to "France"
        And I see the summary of my cart
        Then my cart should be empty

    @ui
    Scenario: Viewing item in cart after switching channels
        Given I changed my current channel to "Poland"
        And I added product "Onion" to the cart
        When I change my current channel to "France"
        And I change back my current channel to "Poland"
        And I see the summary of my cart
        Then there should be one item in my cart
        And this item should have name "Onion"

    @ui
    Scenario: Viewing item in cart after switching channels when product was added in every channel
        Given I changed my current channel to "Poland"
        And I added product "Onion" to the cart
        When I change my current channel to "France"
        And I add product "Banana" to the cart
        And I change back my current channel to "Poland"
        And I see the summary of my cart
        Then there should be one item in my cart
        And this item should have name "Onion"
