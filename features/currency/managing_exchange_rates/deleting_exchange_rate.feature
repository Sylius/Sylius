@managing_exchange_rates
Feature: Deleting exchange rates
    In order to remove obsolete exchange rates
    As an Administrator
    I want to be able to delete an exchange rate

    Background:
        Given the store has currency "US Dollar", "British Pound"
        And I am logged in as an administrator

    @ui
    Scenario: Deleted exchange rate should disappear from the list
        Given the store has an exchange rate 1.2 with base currency "US Dollar" and counter currency "British Pound"
        When I delete the exchange rate between "US Dollar" and "British Pound"
        Then I should be notified that it has been successfully deleted
        And this exchange rate should no longer be on the list

    @ui
    Scenario: Deleting a currency deletes related exchange rates
        Given the store has an exchange rate 1.2 with base currency "US Dollar" and counter currency "British Pound"
        And the store has an exchange rate 0.8 with base currency "British Pound" and counter currency "US Dollar"
        When the currency "US Dollar" gets deleted
        Then there should be no exchange rates on the list
