@locales
Feature: Redirecting to the default locale
    In order to prevent locale deactivation
    As a Customer
    I want to be redirect with default locale if locale is not available

    Background:
        Given the store operates on a channel named "Web"
        And that channel allows to shop using "English (United States)" and "French (France)" locales
        And it uses the "English (United States)" locale by default
        And the store has a product "PHP T-Shirt"

    @ui @api
    Scenario: Staying on the current locale if it is available
        When I browse that channel
        And I use the locale "French (France)"
        Then I should shop using the "French (France)" locale

    @ui @api
    Scenario: Redirecting to default locale if it is not available
        When I browse that channel
        And I use the locale "Polish (Poland)"
        Then I should shop using the "English (United States)" locale
