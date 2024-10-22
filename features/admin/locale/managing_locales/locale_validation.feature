@managing_locales
Feature: Locale validation
    In order to avoid making mistakes when managing locales
    As an Administrator
    I want to be prevented from adding a locale without a code

    Background:
        Given the store has locale "Norwegian (Norway)"
        And I am logged in as an administrator

    @api @no-ui
    Scenario: Trying to add a locale without specifying a code
        When I want to create a new locale
        And I do not choose a code
        And I try to add it
        Then I should be notified that a code is required

    @api @no-ui
    Scenario: Trying to add a locale with a too long code
        When I want to create a new locale
        And I specify a too long code
        And I try to add it
        Then I should be notified that code should be no longer than 10 characters

    @api @no-ui
    Scenario: Trying to add a locale with an invalid code
        When I want to create a new locale
        And I set code to "invalid"
        And I try to add it
        Then I should be notified that the code is invalid

    @api @no-ui
    Scenario: Trying to add a locale with an non-existent code
        When I want to create a new locale
        And I set code to "en_BR"
        And I try to add it
        Then I should be notified that the code is invalid
