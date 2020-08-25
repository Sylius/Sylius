@managing_customers
Feature: Toggling a customer account
    In order to control whether the customer can login or not
    As an Administrator
    I want to be able to disable and enable their user account

    Background:
        Given I am logged in as an administrator

    @ui
    Scenario: Enabling a customer account
        Given there is disabled customer account "f.baggins@example.com" with password "psw"
        And I want to enable "f.baggins@example.com"
        When I enable their account
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this customer should be enabled

    @ui
    Scenario: Disabling a customer account
        Given there is enabled customer account "f.baggins@example.com" with password "psw"
        And I want to disable "f.baggins@example.com"
        When I disable their account
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this customer should be disabled
