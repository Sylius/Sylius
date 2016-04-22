@managing_currencies
Feature: Toggling a currency
    In order to change currencies which are available to my customers
    As an Administrator
    I want to be able to switch state of a currency between enable and disable

    Background:
        Given I am logged in as an administrator

    @ui
    Scenario: Disabling a currency
        Given the store has currency "Euro"
        And I want to edit this currency
        When I disable it
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this currency should be disabled

    @ui
    Scenario: Enabling a currency
        Given the store has disabled currency "Euro"
        And I want to edit this currency
        When I enable it
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this currency should be enabled
