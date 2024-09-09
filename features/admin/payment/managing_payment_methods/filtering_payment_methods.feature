@managing_payment_methods
Feature: Filtering payment methods
    In order to quickly find the payment method I need
    As an Administrator
    I want to filter available payment methods

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a payment method "PayPal Express Checkout" with a code "paypal_xyz" and "Paypal Express Checkout" gateway
        And the store has a payment method "Offline" with a code "offline_abc"
        And this payment method is disabled
        And the store has a payment method "Cash on Delivery" with a code "cash_on_delivery_xyz"
        And I am logged in as an administrator
        And I am browsing payment methods

    @api @ui
    Scenario: Filtering payment methods by name
        When I search by "PayPal" name
        Then I should see a single payment method in the list
        And I should see the payment method "PayPal Express Checkout"

    @api @ui
    Scenario: Filtering payment methods by code
        When I search by "xyz" code
        Then I should see 2 payment methods in the list
        And I should not see the payment method "Offline"

    @api @ui
    Scenario: Filtering enabled payment methods
        When I choose enabled filter
        And I filter
        Then I should see 2 payment methods in the list
        And I should not see the payment method "Offline"
