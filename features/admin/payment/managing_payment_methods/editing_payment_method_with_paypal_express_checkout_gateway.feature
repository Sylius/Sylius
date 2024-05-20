@managing_payment_methods
Feature: Editing payment method configured with a PayPal Express Checkout gateway
    In order to change configuration of a payment method
    As an Administrator
    I want to be able to edit payment method with a PayPal Express Checkout gateway

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a payment method "PayPal Express Checkout" with a code "paypal" and "Paypal Express Checkout" gateway
        And I am logged in as an administrator

    @ui @no-api
    Scenario: Changing gateway username
        When I want to modify the "PayPal Express Checkout" payment method
        And I update its "Username" with "new_username"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And its gateway configuration "Username" should be "new_username"

    @ui @no-api
    Scenario: Changing gateway password
        When I want to modify the "PayPal Express Checkout" payment method
        And I update its "Password" with "new_password"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And its gateway configuration "Password" should be "new_password"

    @ui @no-api
    Scenario: Changing gateway signature
        When I want to modify the "PayPal Express Checkout" payment method
        And I update its "Signature" with "new_signature"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And its gateway configuration "Signature" should be "new_signature"

    @ui @no-api
    Scenario: Changing gateway sandbox mode
        When I want to modify the "PayPal Express Checkout" payment method
        And I enable sandbox mode
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this payment method should be in sandbox mode

    @api @ui
    Scenario: Changing whole gateway configuration
        When I want to modify the "PayPal Express Checkout" payment method
        And I set its "Username" as "new_username", "Password" as "new_password" and "Signature" as "new_signature"
        And I enable sandbox mode
        And I save my changes
        Then I should be notified that it has been successfully edited
        And its gateway configuration "Username" should be "new_username"
        And its gateway configuration "Password" should be "new_password"
        And its gateway configuration "Signature" should be "new_signature"
        And this payment method should be in sandbox mode
