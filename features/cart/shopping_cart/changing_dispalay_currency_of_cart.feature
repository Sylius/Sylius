@shopping_cart
Feature: Changing display currency of the cart
    In order to know estimated price for foreign currencies
    As a Visitor
    I want to see every cash amount rounded to my chosen currency

    Background:
        Given the store operates on a single channel in "United States"
        And that channel allows to shop using the "GBP" currency
        When the exchange rate of "US Dollar" to "British Pound" is 2.0
        And default tax zone is "US"
        And the store has "Pigeon Mail" shipping method with "$5.00" fee
        And the store has "Pugs" tax rate of 10% for "Mugs" within the "US" zone
        And the store has a product "The Pug Mug" priced at "$10.00"
        And it belongs to "Mugs" tax category

    @ui
    Scenario: Changing the currency of my cart
        Given I have product "The Pug Mug" in the cart
        When I switch to the "GBP" currency
        Then my cart total should be "£32.00"
        And total price of "The Pug Mug" item should be "£20.00"
        And my cart taxes should be "£2.00"
        And my cart shipping total should be "£10.00"
