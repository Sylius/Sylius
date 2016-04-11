@managing_payment_methods
Feature: Browsing payment methods
    In order to see all payment methods in the store
    As an Administrator
    I want to be able to browse payment methods

    Background:
        Given the store has a payment method "Offline" with a code "OFF"
        And the store has a payment method "PayPal Express Checkout" with a code "PEC"
        And I am logged in as an administrator

    @todo
    Scenario: Browsing defined payment methods
        When I want to browse payment methods
        Then I should see 2 payment methods in the list
        And the payment method "PayPal Express Checkout" should be in the registry
