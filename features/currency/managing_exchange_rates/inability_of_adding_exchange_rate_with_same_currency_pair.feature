@managing_exchange_rates
Feature: Inability of adding exchange rates with the same currency pair
    In order to have only unique exchange rates
    As an Administrator
    I want to be prevented from adding an exchange rate with same base-counter currency pair

    Background:
        Given the store has currency "Euro", "British Pound"
        And I am logged in as an administrator

    @ui
    Scenario: Being prevented from adding an exchange rate for the same currency pair
        Given the store has an exchange rate 1.2 with base currency "Euro" and counter currency "British Pound"
        And I want to add a new exchange rate
        When I specify its ratio as 3.20
        And I choose "Euro" as the base currency
        And I choose "British Pound" as the counter currency
        And I try to add it
        Then I should be notified that the currency pair must be unique
        And I should still see one exchange rate on the list
        And this exchange rate should have a ratio of 1.2
