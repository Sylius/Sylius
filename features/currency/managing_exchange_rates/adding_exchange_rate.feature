@managing_exchange_rates
Feature: Adding a new exchange rate
    In order to specify exchange rates between different currencies in my store
    As an Administrator
    I want to add a new exchange rate to the store

    Background:
        Given the store has currency "US Dollar" and "British Pound"
        And I am logged in as an administrator

    @ui
    Scenario: Adding a new exchange rate
        Given I want to add a new exchange rate
        When I specify its ratio as 1.20
        And I choose "US Dollar" as the source currency
        And I choose "British Pound" as the target currency
        And I add it
        Then I should be notified that it has been successfully created
        And the exchange rate with ratio 1.20 between "US Dollar" and "British Pound" should appear in the store
