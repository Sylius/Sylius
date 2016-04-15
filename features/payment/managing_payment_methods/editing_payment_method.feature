@managing_payment_methods
Feature: Editing payment methods
    In order to change which payment methods are available in my store
    As an Administrator
    I want to be able to edit payment method

    Background:
        Given the store has a payment method "Offline" with a code "offline"
        And I am logged in as an administrator

    @ui
    Scenario: Renaming the payment method
        Given I want to modify a payment method "Offline"
        When I rename it to "Cash on delivery" in "English (United States)"
        And I save my changes
        Then I should be notified about successful edition
        And this payment method name should be "Cash on delivery"

    @ui
    Scenario: Changing gateway
        Given I want to modify a payment method "Offline"
        When I rename it to "Paypal Express Checkout" in "English (United States)"
        And I choose "Paypal Express Checkout" gateway
        And I save my changes
        Then I should be notified about successful edition
        And this payment method gateway should be "paypal_express_checkout"

    @ui
    Scenario: Disabling payment method
        Given I want to modify a payment method "Offline"
        When I disable it
        And I save my changes
        Then I should be notified about successful edition
        And this payment method should be disabled

    @ui
    Scenario: Enabling payment method
        Given the store has a payment method "Offline" disabled
        And I want to modify a payment method "Offline"
        When I enable it
        And I save my changes
        Then I should be notified about successful edition
        And this payment method should be enabled

    @ui
    Scenario: Seeing disabled code field while editing payment method
        When I want to modify a payment method "Offline"
        Then the code field should be disabled
