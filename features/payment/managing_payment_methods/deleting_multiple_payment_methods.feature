@managing_payment_methods
Feature: Deleting multiple payment methods
    In order to remove test, obsolete or incorrect payment methods in an efficient way
    As an Administrator
    I want to be able to delete multiple payment methods at once

    Background:
        Given the store has a payment method "Offline" with a code "offline"
        And the store has also a payment method "Bank transfer" with a code "transfer"
        And the store has also a payment method "PayPal Express Checkout" with a code "paypal" and Paypal Express Checkout gateway
        And I am logged in as an administrator

    @ui @javascript
    Scenario: Deleting multiple payment methods at once
        When I browse payment methods
        And I check the "Offline" payment method
        And I check also the "Bank transfer" payment method
        And I delete them
        Then I should be notified that they have been successfully deleted
        And I should see a single payment method in the list
        And I should see the payment method "PayPal Express Checkout" in the list
