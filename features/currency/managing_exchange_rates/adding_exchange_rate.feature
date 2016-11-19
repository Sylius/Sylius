@managing_exchange_rates
Feature: Adding a new exchange rate
    In order for the customer to view my goods in different prices according to currency
    As an Administrator
    I want to add a new exchange rate to the store

    Background:
        Given I am logged in as an administrator
        And the store has currency "US Dollar", "British Pound"

    @ui
    Scenario: Adding a new exchange rate
        Given I want to add a new exchange rate
        When I specify its ratio as 1.20
        And I choose "US Dollar" as the base currency
        And I choose "British Pound" as the counter currency
        And I add it
        Then I should be notified that it has been successfully created
        And the exchange rate between "US Dollar" and "British Pound" should appear in the store
