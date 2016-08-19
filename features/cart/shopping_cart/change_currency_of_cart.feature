@shopping_cart
Feature: All of my cart's values get updated to the currency of my choosing
    In order to know how much I have to pay in my currency
    As a Visitor
    I want to see every cash amount in my chosen currency

    Background:
        Given the store operates on a single channel in "France"
        And that channel allows to shop using the "EUR" currency
        And that channel also allows to shop using the "GBP" currency with exchange rate 2.00
        And that channel uses the "EUR" currency by default
        And the store has a product "The Pug Mug" priced at "€10.00"

    @ui
    Scenario: Changing the currency of my cart
        Given I have product "The Pug Mug" in the cart
        When I switch to the "GBP" currency
        Then my cart total should be "£20.00"
        And total price of "The Pug Mug" item should be "£20.00"
        And my cart taxes should be "£0.00"
        And my cart shipping total should be "£0.00"
