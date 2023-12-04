@managing_customers
Feature: Toggling a customer account
    In order to control whether the customer is verified
    As an Administrator
    I want to be able to verify their account

    Background:
        Given I am logged in as an administrator

    @api @ui
    Scenario: Verifying customer account
        Given there is enabled customer account "f.baggins@example.com" with password "psw"
        When I want to verify "f.baggins@example.com"
        And I verify it
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this customer should be verified
