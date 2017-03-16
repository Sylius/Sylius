@managing_payment_methods
Feature: Editing payment methods
    In order to change which payment methods are available in my store
    As an Administrator
    I want to be able to edit payment method

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a payment method "Offline" with a code "offline"
        And I am logged in as an administrator

    @ui
    Scenario: Renaming the payment method
        Given I want to modify the "Offline" payment method
        When I rename it to "Cash on delivery" in "English (United States)"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this payment method name should be "Cash on delivery"

    @ui
    Scenario: Disabling payment method
        Given I want to modify the "Offline" payment method
        When I disable it
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this payment method should be disabled

    @ui
    Scenario: Enabling payment method
        Given the payment method "Offline" is disabled
        And I want to modify the "Offline" payment method
        When I enable it
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this payment method should be enabled

    @ui
    Scenario: Seeing disabled code field while editing payment method
        When I want to modify the "Offline" payment method
        Then the code field should be disabled

    @ui
    Scenario: Seeing disabled gateway factory field while editing payment method
        When I want to modify the "Offline" payment method
        Then the factory name field should be disabled
