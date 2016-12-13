@managing_exchange_rates
Feature: Filtering exchange rates by a currency
    In order to filter exchange rates by a specific currency
    As an Administrator
    I want to be able to filter exchange rates in the store

    Background:
        Given the store has currency "Euro", "British Pound" and "Polish Zloty"
        And the exchange rate of "Euro" to "British Pound" is 0.84
        And the exchange rate of "British Pound" to "Polish Zloty" is 5.31
        And the exchange rate of "Polish Zloty" to "Euro" is 0.22
        And I am logged in as an administrator

    @ui
    Scenario: Filtering exchange rates by a chosen currency
        When I browse exchange rates of the store
        And I choose "Euro" as a currency filter
        And I filter
        Then I should see 2 exchange rates on the list
        And I should see an exchange rate between "Euro" and "British Pound" on the list
        And I should also see an exchange rate between "Polish Zloty" and "Euro" on the list
