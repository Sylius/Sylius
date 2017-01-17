@managing_exchange_rates
Feature: Deleting exchange rates
    In order to remove obsolete exchange rates
    As an Administrator
    I want to be able to delete an exchange rate

    Background:
        Given the store has currency "US Dollar", "British Pound" and "Bhutanese Ngultrum"
        And I am logged in as an administrator

    @ui
    Scenario: Deleted exchange rate should disappear from the list
        Given the exchange rate of "US Dollar" to "British Pound" is 1.2
        When I delete the exchange rate between "US Dollar" and "British Pound"
        Then I should be notified that it has been successfully deleted
        And this exchange rate should no longer be on the list
