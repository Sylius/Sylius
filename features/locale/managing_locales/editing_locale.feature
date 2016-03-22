@locale
Feature: Editing a locale
    In order to enable or disable locales
    As an Administrator
    I want to be able to edit a locale

    Background:
        Given I am logged in as administrator

    @ui
    Scenario: Disabling the locale
        Given the store has locale "Norwegian"
        And I want to edit this locale
        When I disable it
        And I save my changes
        Then I should be notified about successful edition
        And this locale should be disabled

    @todo
    Scenario: Enabling the locale
        Given the store has disabled locale "Norwegian"
        And I want to edit this locale
        When I enable it
        And I save my changes
        Then I should be notified about successful edition
        And this locale should be enabled
