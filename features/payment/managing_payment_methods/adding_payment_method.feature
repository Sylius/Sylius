@managing_payment_methods
Feature: Adding a new payment method
    In order to pay for orders in different ways
    As an Administrator
    I want to add a new payment method to the registry

    Background:
        Given the store is available in "English (United States)"
        And I am logged in as an administrator

    @ui
    Scenario: Adding a new payment method
        Given I want to create a new payment method
        When I name it "Paypal Express Checkout" in "English (United States)"
        And I specify its code as "PEC"
        And I choose "Paypal Express Checkout" gateway
        And I add it
        Then I should be notified that it has been successfully created
        And the payment method "Paypal Express Checkout" should appear in the registry

    @ui
    Scenario: Adding a new payment method with description
        Given I want to create a new payment method
        When I name it "Paypal Express Checkout" in "English (United States)"
        And I specify its code as "PEC"
        And I choose "Paypal Express Checkout" gateway
        And I describe it as "Payment method Paypal Express Checkout" in "English (United States)"
        And I add it
        Then I should be notified that it has been successfully created
        And the payment method "Paypal Express Checkout" should appear in the registry
