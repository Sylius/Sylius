@payments
Feature: Payment methods
    In order to allow customers to select a payment method
    As a store owner
    I want to be able to manage payment methods

    Background:
        Given I am logged in as administrator
        And there is default currency configured
        And the following payment methods exist:
            | name        | gateway |
            | Credit Card | stripe  |
            | PayPal      | paypal  |

    Scenario: Seeing index of all payment methods
        When I go to the payment method index page
        Then I should see 2 payment methods in the list

    Scenario: Seeing empty index of payment methods
        Given there are no payment methods
        When I am on the payment method index page
        Then I should see "There are no payment methods configured"

    Scenario: Accessing the payment method creation form
        Given I am on the payment method index page
        When I follow "Create payment method"
        Then I should be on the payment method creation page

    Scenario: Submitting invalid form without name
        Given I am on the payment method creation page
        When I press "Save"
        Then I should still be on the payment method creation page
        And I should see "Please enter payment method name."

    Scenario: Creating new payment method
        Given I am on the payment method creation page
        When I fill in "Name" with "Google Checkout"
        And I press "Save"
        Then I should be on the payment method index page
        And I should see "Payment method has been successfully created."
        And I should see payment method with name "Google Checkout" in the list

    Scenario: Accessing the editing form from the list
        Given I am on the payment method index page
        When I click "edit" near "PayPal"
        Then I should be editing payment method "PayPal"

    Scenario: Updating the payment method
        Given I am editing payment method "PayPal"
        When I fill in "Name" with "PayPal PRO"
        And I press "Save changes"
        Then I should be on the payment method index page
        And I should see payment method with name "PayPal PRO" in the list

    Scenario: Deleting payment method
        Given I am on the payment method index page
        When I click "delete" near "PayPal"
        Then I should still be on the payment method index page
        And I should see "Payment method has been successfully deleted."
        And I should not see payment method with name "PayPal" in that list
