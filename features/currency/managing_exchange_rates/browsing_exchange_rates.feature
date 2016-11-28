@managing_exchange_rates
Feature: Browsing exchange rates
    In order to manage exchange rates used in the store
    As an Administrator
    I want to browse exchange rates

    Background:
        Given the store has currency "Euro", "British Pound" and "Bhutanese Ngultrum"
        And the exchange rate of "Euro" to "British Pound" is 1.2
        And the exchange rate of "British Pound" to "Bhutanese Ngultrum" is 2.37
        And I am logged in as an administrator

    @ui
    Scenario: Browsing store's exchange rates
        When I am browsing exchange rates of the store
        Then I should see 2 exchange rates on the list
        And I should see an exchange rate between "British Pound" and "Bhutanese Ngultrum" on the list
