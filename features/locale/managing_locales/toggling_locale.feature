@managing_locales
Feature: Toggling a locale
    In order to change locales in which the store is available to my customers
    As an Administrator
    I want to be able to change availability of locales in store

    Background:
        Given the store operates on a single channel
        And that channel allows to shop using "English (United States)", "Spanish (Mexico)" and "Portuguese (Brazil)" locales
        And it uses the "English (United States)" locale by default
        And I am logged in as an administrator

    @ui
    Scenario: Disabling the locale
        Given the locale "Spanish (Mexico)" is enabled
        And I want to edit this locale
        When I disable it
        And I save my changes
        Then the store should not be available in the "Spanish (Mexico)" locale

    @ui
    Scenario: Enabling the locale
        Given the locale "Portuguese (Brazil)" is disabled
        And I want to edit this locale
        When I enable it
        And I save my changes
        Then the store should be available in the "Portuguese (Brazil)" locale
