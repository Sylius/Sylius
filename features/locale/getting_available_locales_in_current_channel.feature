@locales
Feature: Getting available locales in the current channel
    In order to use a shop in chosen locale
    As a Customer
    I want to be able to get available locales

    Background:
        Given the store operates on a channel named "Web"
        And that channel allows to shop using "English (United States)" and "Polish (Poland)" locales
        And it uses the "English (United States)" locale by default
        And the store has locale "German (Germany)"

    @api
    Scenario: Getting only available locales in channel
        When I get available locales
        Then I should have 2 locales
        And the "English (United States)" locale with code "en_US" should be available
        And the "Polish (Poland)" locale with code "pl_PL" should be available
        But the "German (Germany)" locale with code "de_DE" should not be available

    @api
    Scenario: Get locales details
        When I get "English (United States)" locale
        Then I should have "English (United States)" with code "en_US"
