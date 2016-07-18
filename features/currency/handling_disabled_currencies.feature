@currencies
Feature: Handling disabled currencies
    In order to see right prices
    As a Customer
    I want to browse channels with a valid currency only

    Background:
        Given the store operates on a channel named "Web"
        And that channel allows to shop using "EUR", "USD" and "GBP" currencies
        And it uses the "EUR" currency by default
        But the currency "GBP" is disabled

    Scenario: Not showing the disabled currency
        When I browse that channel
        Then I should not be able to shop using the "GBP" currency

    Scenario: Failing to browse channel with disabled default currency
        Given the currency "EUR" is disabled as well
        When I try to browse that channel
        Then I should receive an error

    Scenario: Browsing a channel with the default currency disabled while using the other one
        Given the currency "EUR" is disabled as well
        When I browse that channel while using the "USD" currency
        Then I should shop using the "USD" currency
        And I should not be able to shop using the "EUR" currency
