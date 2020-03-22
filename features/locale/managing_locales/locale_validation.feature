@managing_locales
Feature: Locale validation
    In order to avoid making mistakes when managing locales
    As an Administrator
    I want to be prevented from adding a locale without a code

    Background:
        Given the store has locale "Norwegian (Norway)"
        And I am logged in as an administrator

    @api
    Scenario: Trying to add a locale without specifying a code
        Given I want to create a new locale
        When I do not choose a code
        And I try to add it
        Then I should be notified that a code is required
