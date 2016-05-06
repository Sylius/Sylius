@managing_currencies
Feature: Editing a currency
    In order to change currency configuration
    As an Administrator
    I want to be able to edit a currency

    Background:
        Given I am logged in as an administrator

    @ui
    Scenario: Change exchange rate of a currency
        Given the store has currency "Euro"
        And I want to edit this currency
        When I change exchange rate to 0.786
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this currency should have exchange rate 0.786

    @ui
    Scenario: Seeing disabled code field while editing currency
        Given the store has currency "Euro"
        When I want to edit this currency
        Then the code field should be disabled
