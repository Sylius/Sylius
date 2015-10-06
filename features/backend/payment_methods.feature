@payments
Feature: Payment methods
    In order to allow customers to select a payment method
    As a store owner
    I want to be able to manage payment methods

    Background:
        Given store has default configuration
          And the following payment methods exist:
            | name        | gateway | calculator | calculator_configuration |
            | Credit Card | stripe  | fixed      | amount: 10               |
            | PayPal      | paypal  | percent    | percent: 5               |
          And I am logged in as administrator

    Scenario: Seeing index of all payment methods
        Given I am on the dashboard page
         When I follow "Payment methods"
         Then I should be on the payment method index page
          And I should see 2 payment methods in the list

    Scenario: Seeing empty index of payment methods
        Given there are no payment methods
         When I am on the payment method index page
         Then I should see "There are no payment methods configured"

    Scenario: Accessing the payment method creation form
        Given I am on the dashboard page
         When I follow "Payment methods"
          And I follow "Create payment method"
         Then I should be on the payment method creation page

    Scenario: Submitting invalid form without name
        Given I am on the payment method creation page
         When I press "Create"
         Then I should still be on the payment method creation page
          And I should see "Please enter payment method name."

    Scenario: Creating new payment method with flexible rate
        Given I am on the payment method creation page
         When I fill in "Name" with "Google Checkout"
          And I fill in "Amount" with "10"
          And I press "Create"
         Then I should be on the payment method index page
          And I should see "Payment method has been successfully created."

    Scenario: Describing the payment method
        Given I am on the payment method creation page
         When I fill in "Name" with "Google Checkout"
          And I fill in "Description" with "Flexible checkout by Google!"
          And I fill in "Amount" with "10"
          And I press "Create"
         Then I should be on the payment method index page
          And I should see "Payment method has been successfully created."

    Scenario: Created methods appear in the list
        Given I am on the payment method creation page
         When I fill in "Name" with "PayU"
          And I fill in "Amount" with "10"
          And I press "Create"
         Then I should be on the payment method index page
          And I should see payment method with name "PayU" in the list

    Scenario: Accessing the editing form from the list
        Given I am on the payment method index page
         When I click "edit" near "PayPal"
         Then I should be editing payment method "PayPal"

    Scenario: Updating the payment method
        Given I am editing payment method "PayPal"
         When I fill in "Name" with "PayPal PRO"
          And I fill in "%" with "10"
          And I press "Save changes"
         Then I should be on the payment method index page
          And I should see payment method with name "PayPal PRO" in the list

    Scenario: Submitting invalid form without percent
        Given I am editing payment method "PayPal"
         When I fill in "Name" with "PayPal PRO"
          And I leave "%" empty
          And I press "Save changes"
         Then I should be editing payment method "PayPal"
          And I should see "Please enter the fee percent."

    Scenario: Submitting invalid form with negative percent
        Given I am editing payment method "PayPal"
         When I fill in "Name" with "PayPal PRO"
          And I fill in "%" with "-1"
          And I press "Save changes"
         Then I should be editing payment method "PayPal"
          And I should see "Percent fee cannot be lower than 0."

    Scenario: Submitting invalid form with percent over 100
        Given I am editing payment method "PayPal"
         When I fill in "Name" with "PayPal PRO"
          And I fill in "%" with "120"
          And I press "Save changes"
         Then I should be editing payment method "PayPal"
          And I should see "Percent fee cannot be greater than 100."

    Scenario: Submitting invalid form without amount
        Given I am editing payment method "Credit Card"
         When I fill in "Name" with "Master Card"
          And I leave "Amount" empty
          And I press "Save changes"
         Then I should be editing payment method "Credit Card"
          And I should see "Please enter the fee amount."

    Scenario: Submitting invalid form with negative amount
        Given I am editing payment method "Credit Card"
         When I fill in "Name" with "Master Card"
          And I fill in "Amount" with "-1"
          And I press "Save changes"
         Then I should be editing payment method "Credit Card"
          And I should see "The fee cannot be lower than 0."

    @javascript
    Scenario: Deleting payment method
        Given I am on the payment method index page
         When I click "delete" near "PayPal"
          And I click "delete" from the confirmation modal
         Then I should still be on the payment method index page
          And I should see "Payment method has been successfully deleted."

    @javascript
    Scenario: Deleted payment method disappears from the list
        Given I am on the payment method index page
         When I click "delete" near "PayPal"
          And I click "delete" from the confirmation modal
         Then I should still be on the payment method index page
          And I should not see payment method with name "PayPal" in that list
