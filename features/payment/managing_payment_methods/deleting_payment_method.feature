@managing_payment_methods
Feature: Deleting payment methods
    In order to remove test, obsolete or incorrect payment methods
    As an Administrator
    I want to be able to delete a payment method

    Background:
        Given the store has a payment method "Offline" with a code "offline"
        And I am logged in as an administrator

    @ui
    Scenario: Deleted payment method should disappear from the registry
        When I delete the "Offline" payment method
        Then I should be notified that it has been successfully deleted
        And this payment method should no longer exist in the registry
