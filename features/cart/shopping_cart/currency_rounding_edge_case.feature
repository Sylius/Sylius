@shopping_cart
Feature: Currency rounding edge case
    In order to pay proper value of my cart
    As a Visitor
    I want to have total value of my cart items rounded properly

    Background:
        Given the store operates on a single channel in "United States"
        And that channel allows to shop using the "EUR" currency
        When the exchange rate of "US Dollar" to "Euro" is 0.9737
        And the store has a product "The Pug Mug" priced at "$7.00"

    @ui
    Scenario: Changing the currency of my cart
        Given I have product "The Pug Mug" in the cart
        When I switch to the "EUR" currency
        Then the grand total value should be "€6.82"
        And the grand total value in base currency should be "$7.00"

    @ui
    Scenario: Changing the currency of my cart
        Given I have added 2 products "The Pug Mug" to the cart
        When I switch to the "EUR" currency
        Then the grand total value in base currency should be "$14.00"
        And I should see "The Pug Mug" with unit price "€6.82" in my cart
        But the grand total value should be "€13.63"
