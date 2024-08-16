@managing_currencies
Feature: Currency unique code validation
    In order to uniquely identify currency
    As an Administrator
    I want to be prevented from adding two currency with the same code

    Background:
        Given the store has currency "Euro"
        And I am logged in as an administrator

    @api @no-ui
    Scenario: Trying to add currency with taken code
        When I want to add a new currency
        And I choose "Euro"
        And I try to add it
        Then I should be notified that currency code must be unique
        And there should still be only one currency with code "EUR"

    @no-api @ui @mink:chromedriver
    Scenario: Trying to add new currency with used code
        When I want to add a new currency
        Then I should not be able to choose "Euro"
