@managing_currencies
Feature: Inability of modifying the base currency
    In order to always have the base currency available in the store with base exchange rate
    As an Administrator
    I want to be prevented from modifying the base currency

    Background:
        Given I am logged in as an administrator

    @ui
    Scenario: Being prevented from disabling base currency
        Given the store operates on a single channel
        And it uses the "USD" currency by default
        When I want to edit this currency
        Then I should not be able to disable this currency

    @ui
    Scenario: Being prevented from changing base currency exchange rate
        Given the store operates on a single channel
        And it uses the "USD" currency by default
        When I want to edit this currency
        Then I should not be able to change exchange rate of this currency
