@currencies
Feature: Handling disabled currencies
    In order to see right prices
    As a Customer
    I want to browse channels only with valid currency

    Background:
        Given the store operates on a channel named "Web"
        And that channel allows for "EUR", "USD" and "GBP" currencies
        And it uses "EUR" currency by default
        But currency "GBP" is disabled

    Scenario: Not showing the disabled currency
        When I browse that channel
        Then I should not be able to shop using "GBP" currency

    Scenario: Failing to browse channel with disabled default currency
        Given currency "EUR" is disabled as well
        When I try to browse that channel
        Then I should receive an error

    Scenario: Browsing a channel with disabled default currency while using the other one
        Given currency "EUR" is disabled as well
        When I browse that channel while using "USD" currency
        Then I should shop using "USD" currency
        And I should not be able to shop using "EUR" currency
