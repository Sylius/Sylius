@managing_customers
Feature: Resetting a customer's password
    In order to help customers to retrieve access to theirs accounts
    As an Administrator
    I want to be able to reset customer's password

    Background:
        Given there is a user "goodman@example.com" identified by "heisenberg"
        And I am logged in as an administrator

    @ui @todo
    Scenario: Sending reset password email to a customer
        When I view details of the customer "goodman@example.com"
        And I reset their password
        Then I should be notified that email with reset instruction has been send
        And the email with reset token should be sent to "goodman@example.com"
