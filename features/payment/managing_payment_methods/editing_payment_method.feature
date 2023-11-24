@managing_payment_methods
Feature: Editing payment methods
    In order to change which payment methods are available in my store
    As an Administrator
    I want to be able to edit payment method

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a payment method "Offline" with a code "Offline"
        And I am logged in as an administrator

    @ui @api
    Scenario: Renaming the payment method
        When I want to modify the "Offline" payment method
        And I rename it to "Cash on delivery" in "English (United States)"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this payment method name should be "Cash on delivery"

    @ui @api
    Scenario: Disabling payment method
        When I want to modify the "Offline" payment method
        And I disable it
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this payment method should be disabled

    @ui @api
    Scenario: Enabling payment method
        Given the payment method "Offline" is disabled
        When I want to modify the "Offline" payment method
        And I enable it
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this payment method should be enabled

    @ui @api
    Scenario: Being unable to edit code of an existing payment method
        When I want to modify the "Offline" payment method
        Then I should not be able to edit its code

    @ui @api
    Scenario: Being unable to edit gateway factory field of existing payment method
        When I want to modify the "Offline" payment method
        Then the factory name field should be disabled
