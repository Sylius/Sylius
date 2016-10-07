@managing_currencies
Feature: Inability of disabling the base currency
    In order to always have the base currency available in the store
    As an Administrator
    I want to be prevented from disabling the base currency

    Background:
        Given I am logged in as an administrator

    @ui
    Scenario:
        Given the store operates on a single channel
        And it uses the "USD" currency by default
        When I want to edit this currency
        Then I should not be able to disable this currency
