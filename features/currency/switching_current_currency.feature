@currencies
Feature: Switching the current currency
    In order to see prices in my preferred currency
    As a Customer
    I want to be able to switch currencies

    Background:
        Given the store operates on a channel named "Web"
        And that channel allows for "EUR" and "USD" currencies
        And it uses "EUR" currency by default

    Scenario: Showing the current currency
        When I browse that channel
        Then I should shop using "EUR" currency

    Scenario: Showing the available currencies
        When I browse that channel
        Then I should be able to shop using "USD" currency

    Scenario: Switching the current currency
        When I browse that channel
        And I switch the current currency to "USD" currency
        Then I should shop using "USD" currency
        And I should be able to shop using "EUR" currency
