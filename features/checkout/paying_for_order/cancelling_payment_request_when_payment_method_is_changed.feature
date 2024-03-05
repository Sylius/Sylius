@paying_for_order
Feature: Cancelling payment request when payment method is changed
    In order to pay with the correct payment method
    As a Customer
    I want my payment request with wrong payment method to be cancelled

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "Iron Maiden T-Shirt"
        And the store ships everywhere for Free
        And the store allows paying Offline
        And the store has a payment method "PayPal" with a code "PAYPAL" and Paypal Express Checkout gateway
        And there is a customer "sylius@example.com" that placed an order "#00000022"

    @no-ui @api
    Scenario: Cancelling the payment request when payment method is changed
        Given I added product "Iron Maiden T-Shirt" to the cart
        And I have proceeded selecting "PayPal" payment method
        And I completed my order and requested paypal payment
        When I change payment method to "Offline" after checkout
        Then the payment request for payment method "PayPal" should be cancelled
