@currencies
Feature: Handling different currencies on multiple channels
    In order to see right prices
    As a Customer
    I want to browse channels only with valid currency

    Background:
        Given the store operates on a channel named "Web"
        And that channel allows for "EUR", "USD" and "GBP" currencies
        And it uses "EUR" currency by default
        And the store operates on another channel named "Mobile"
        And that channel allows for "USD" and "GBP" currencies
        And it uses "USD" currency by default

    Scenario: Showing currencies only from the current channel
        When I browse "Mobile" channel
        Then I should shop using "USD" currency
        And I should be able to shop using "GBP" currency
        And I should not be able to shop using "EUR" currency

    Scenario: Browsing channels using their default currencies
        When I browse "Web" channel
        And I start browsing "Mobile" channel
        Then I should shop using "USD" currency

    Scenario: Switching currency applies only to the current channel
        When I browse "Web" channel
        And I switch the current currency to "GBP" currency
        And I start browsing "Mobile" channel
        Then I should still shop using "USD" currency
