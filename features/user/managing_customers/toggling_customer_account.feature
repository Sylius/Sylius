@managing_customers
Feature: Toggling a customer account
    In order to change information about state of a customer account
    As an Administrator
    I want to be able to switch state of a customer account between enable and disable

    Background:
        Given I am logged in as an administrator

    @ui
    Scenario: Enabling a customer account
        Given there is disabled customer account "f.baggins@example.com" with password "psw"
        And I want to enable "f.baggins@example.com"
        When I enable it
        And I save my changes
        Then I should be notified that it has been successfully edited
        And the customer "f.baggins@example.com" should be enabled

    @ui
    Scenario: Disabling a customer account
        Given there is enabled customer account "f.baggins@example.com" with password "psw"
        And I want to disable "f.baggins@example.com"
        When I disable it
        And I save my changes
        Then I should be notified that it has been successfully edited
        And the customer "f.baggins@example.com" should be disabled
