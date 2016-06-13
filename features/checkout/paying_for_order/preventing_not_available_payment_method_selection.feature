@checkout
Feature: Preventing not available payment method selection
    In order to pay for my order properly
    As a Customer
    I want to be prevented from selecting not available payment methods

    Background:
        Given the store operates on a single channel in "France"
        And the store has a product "PHP T-Shirt" priced at "$19.99"
        And the store has "Offline" payment method
        And the store has "Raven Post" shipping method with "€10.00" fee
        And I am logged in as customer

    @todo
    Scenario: Not being able to select disabled payment method
        Given the store has "Offline" payment method
        And the store has disabled "Paypal Express" payment method
        And I have product "PHP T-Shirt" in the cart
        When I am at the checkout shipment step
        And  I select "Raven Post" shipping method
        And I complete the shipping step
        Then I should not be able to select "Paypal Express" payment method
