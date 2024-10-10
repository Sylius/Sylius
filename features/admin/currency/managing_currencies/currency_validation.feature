@managing_currencies
Feature: Currency validation
    In order to avoid making mistakes when managing currencies
    As an Administrator
    I want to be prevented from adding an invalid currency

    Background:
        Given I am logged in as an administrator

    @api @no-ui
    Scenario: Trying to add currency without code
        When I want to add a new currency
        And I do not choose a code
        And I try to add it
        Then I should be notified that a code is required

    @api @no-ui
    Scenario: Trying to add a currency with an invalid code
        When I want to add a new currency
        And I set code to "invalid"
        And I try to add it
        Then I should be notified that the code is invalid

    @api @no-ui
    Scenario: Trying to add a currency with an non-existent code
        When I want to add a new currency
        And I set code to "B0B"
        And I try to add it
        Then I should be notified that the code is invalid
