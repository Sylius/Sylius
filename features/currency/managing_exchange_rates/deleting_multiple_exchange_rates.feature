@managing_exchange_rates
Feature: Deleting multiple exchange rates
    In order to remove obsolete exchange rates in an efficient way
    As an Administrator
    I want to be able to delete multiple exchange rates at once

    Background:
        Given the store has currency "Euro", "British Pound" and "Polish Zloty"
        And the exchange rate of "Euro" to "British Pound" is 0.84
        And the exchange rate of "British Pound" to "Polish Zloty" is 5.31
        And the exchange rate of "Polish Zloty" to "Euro" is 0.22
        And I am logged in as an administrator

    @ui @javascript
    Scenario: Deleting multiple exchange rates at once
        When I browse exchange rates
        And I check the exchange rate between "Euro" and "British Pound"
        And I check the exchange rate between "British Pound" and "Polish Zloty"
        And I delete them
        Then I should be notified that they have been successfully deleted
        And I should see a single exchange rate in the list
        And I should see the exchange rate between "Polish Zloty" and "Euro" in the list
