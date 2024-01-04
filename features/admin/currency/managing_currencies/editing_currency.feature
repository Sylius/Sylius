@managing_currencies
Feature: Editing a currency
    In order to change currency configuration
    As an Administrator
    I want to be able to edit a currency

    Background:
        Given I am logged in as an administrator

    @ui
    Scenario: Seeing disabled code field while editing currency
        Given the store has currency "Euro"
        When I want to edit this currency
        Then the code field should be disabled
