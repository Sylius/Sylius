@shopping_cart
Feature: Currency rounding edge case
    In order to pay proper value of my cart
    As a Visitor
    I want to have total value of my cart items rounded properly

    Background:
        Given the store operates on a single channel in "United States"
        And that channel allows to shop using the "EUR" currency with exchange rate 0.9737
        And default tax zone is "US"
        And the store has "Pigeon Mail" shipping method with "$5.00" fee
        And the store has "VAT" tax rate of 10% for "Mugs" within the "US" zone
        And the store has a product "The Pug Mug" priced at "$7.00"
        And it belongs to "Mugs" tax category

    @ui
    Scenario: Changing the currency of my cart
        Given I have product "The Pug Mug" in the cart
        When I switch to the "EUR" currency
        Then total price of "The Pug Mug" item should be "€6.82"

    @ui
    Scenario: Changing the currency of my cart
        Given I have added 2 products "The Pug Mug" to the cart
        When I switch to the "EUR" currency
        Then total price of "The Pug Mug" item should be "€13.84"
