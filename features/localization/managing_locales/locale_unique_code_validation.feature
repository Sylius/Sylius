@localization
Feature: Locale unique code validation
    In order to avoid making mistakes when managing locales
    As an Administrator
    I want to be prevented from adding a locale with an existing code

    Background:
        Given the store is available in the Norwegian language
        And I am logged in as administrator

    @todo
    Scenario: Trying to add new locale with used code
        Given I want to create new locale
        When I choose Norwegian
        And I try to add it
        Then I should be notified that it is not possible
