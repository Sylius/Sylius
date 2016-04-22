@managing_locales
Feature: Locale unique code validation
    In order to avoid making mistakes when managing locales
    As an Administrator
    I want to be prevented from adding a locale with an existing code

    Background:
        Given the store has locale "Norwegian (Norway)"
        And I am logged in as an administrator

    @ui
    Scenario: Trying to add new locale with used code
        Given I want to add a new locale
        Then I should not be able to choose "Norwegian (Norway)"
