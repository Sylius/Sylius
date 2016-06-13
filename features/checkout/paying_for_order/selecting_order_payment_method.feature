@checkout
Feature: Selecting an order payment method
    In order to pay for my order
    As a Customer
    I want to be able to choose a payment method

    Background:
        Given the store operates on a single channel in "France"
        And the store has a product "PHP T-Shirt" priced at "$19.99"
        And the store has "Offline" payment method
        And I am logged in as customer

    @todo
    Scenario: Selecting a payment method
        Given I have product "PHP T-Shirt" in the cart
        And I am at the checkout payment step
        When I choose "Offline" payment method
        And I complete the payment step
