@managing_exchange_rates
Feature: Browsing exchange rates
    In order to manage exchange rates used in the store
    As an Administrator
    I want to browse exchange rates

    Background:
        Given the store has currency "Euro", "British Pound" and "Bhutanese Ngultrum"
        And the store has an exchange rate 1.2 with source currency "Euro" and target currency "British Pound"
        And the store also has an exchange rate 2.37 with source currency "British Pound" and target currency "Bhutanese Ngultrum"
        And I am logged in as an administrator

    @ui
    Scenario: Browsing store's exchange rates
        When I am browsing exchange rates of the store
        Then I should see 2 exchange rates on the list
        And I should see an exchange rate between "British Pound" and "Bhutanese Ngultrum" on the list
