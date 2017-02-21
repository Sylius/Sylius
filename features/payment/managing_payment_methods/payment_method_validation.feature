@managing_payment_methods
Feature: Payment method validation
    In order to avoid making mistakes when managing a payment method
    As an Administrator
    I want to be prevented from adding it without specifying required fields

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a payment method "Offline" with a code "offline"
        And I am logged in as an administrator

    @ui
    Scenario: Trying to add a new payment method without specifying its code
        Given I want to create a new payment method with "Paypal Express Checkout" gateway factory
        When I name it "Paypal Express Checkout" in "English (United States)"
        But I do not specify its code
        And I add it
        Then I should be notified that code is required
        And the payment method with name "Paypal Express Checkout" should not be added

    @ui
    Scenario: Trying to add a new payment method without specifying its name
        Given I want to create a new payment method with "Paypal Express Checkout" gateway factory
        When I specify its code as "PEC"
        But I do not name it
        And I add it
        Then I should be notified that name is required
        And the payment method with code "PEC" should not be added

    @ui
    Scenario: Trying to add a new paypal payment method without specifying required configuration
        Given I want to create a new payment method with "Paypal Express Checkout" gateway factory
        When I name it "Paypal Express Checkout" in "English (United States)"
        And I specify its code as "PEC"
        And I configure it for username "TEST" with "TEST" signature
        But I do not specify configuration password
        And I add it
        Then I should be notified that I have to specify paypal password
        And the payment method with code "PEC" should not be added

    @ui
    Scenario: Trying to remove name from an existing payment method
        Given I want to modify the "Offline" payment method
        When I remove its name from "English (United States)" translation
        And I try to save my changes
        Then I should be notified that name is required
        And this payment method should still be named "Offline"
