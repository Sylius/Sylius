@currencies
Feature: Handling different currencies on multiple channels
    In order to see right prices
    As a Customer
    I want to browse channels with a valid currency only

    Background:
        Given the store operates on a channel named "Web" in "EUR" currency
        And that channel allows to shop using "EUR", "USD" and "GBP" currencies
        And the store operates on another channel named "Mobile" in "USD" currency
        And that channel allows to shop using "USD" and "GBP" currencies

    @ui
    Scenario: Showing currencies only from the current channel
        When I browse the "Mobile" channel
        Then I should shop using the "USD" currency
        And I should be able to shop using the "GBP" currency
        And I should not be able to shop using the "EUR" currency

    @ui
    Scenario: Browsing channels using their default currencies
        When I browse the "Web" channel
        And I start browsing the "Mobile" channel
        Then I should shop using the "USD" currency

    @ui
    Scenario: Switching a currency applies only to the current channel
        When I browse the "Web" channel
        And I switch to the "GBP" currency
        And I start browsing the "Mobile" channel
        Then I should still shop using the "USD" currency
