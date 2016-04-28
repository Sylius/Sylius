@managing_locales
Feature: Editing a locale
    In order to enable or disable locales
    As an Administrator
    I want to be able to edit a locale

    Background:
        Given I am logged in as an administrator

    @ui
    Scenario: Disabling a locale
        Given the store has locale "Norwegian"
        And I want to edit this locale
        When I disable it
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this locale should be disabled

    @ui
    Scenario: Enabling a locale
        Given the store has disabled locale "Norwegian"
        And I want to edit this locale
        When I enable it
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this locale should be enabled
