@managing_payment_methods
Feature: Payment method unique code validation
    In order to uniquely identify payment methods
    As an Administrator
    I want to be prevented from adding two payment methods with the same code

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a payment method "Offline" with a code "offline"
        And I am logged in as an administrator

    @ui
    Scenario: Trying to add payment method with taken code
        Given I want to create a new offline payment method
        When I name it "Paypal Express Checkout" in "English (United States)"
        And I specify its code as "offline"
        And I try to add it
        Then I should be notified that payment method with this code already exists
        And there should still be only one payment method with code "offline"
