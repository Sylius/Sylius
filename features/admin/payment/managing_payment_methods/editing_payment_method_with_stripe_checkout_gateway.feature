@managing_payment_methods
Feature: Editing payment method configured with a Stripe Checkout gateway
    In order to change which payment methods are available in my store
    As an Administrator
    I want to be able to edit payment method

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a payment method "Stripe Checkout" with a code "stripe" and "Stripe Checkout" gateway
        And I am logged in as an administrator

    @no-api @ui
    Scenario: Changing Stripe Checkout gateway publishable key
        When I want to modify the "Stripe Checkout" payment method
        And I update its "Publishable key" with "some_publishable_key"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this payment method "Publishable key" should be "some_publishable_key"

    @no-api @ui
    Scenario: Changing Stripe Checkout gateway secret key
        When I want to modify the "Stripe Checkout" payment method
        And I update its "Secret key" with "some_secret_key"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this payment method "Secret key" should be "some_secret_key"

    @api @ui
    Scenario: Changing whole gateway configuration
        When I want to modify the "Stripe Checkout" payment method
        And I set its "Publishable key" as "new_publishable_key" and "Secret key" as "new_secret_key"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And its gateway configuration "Publishable key" should be "new_publishable_key"
        And its gateway configuration "Secret key" should be "new_secret_key"
