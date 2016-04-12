@managing_locales
Feature: Toggling a locale
    In order to change locales in which the service is available to my customers
    As an Administrator
    I want to be able to switch state of locale between enable and disable

    Background:
        Given the store is available in the Armenian language
        And I am logged in as an administrator

    @todo
    Scenario: Disabling the locale
        Given the Armenian language is enabled
        When I disable the Armenian language
        Then I should be notified about success
        And this locale should still appear in the registry
        But it should be disabled
        And store should not be available in the Armenian language

    @todo
    Scenario: Enabling the locale
        Given the Armenian language is disabled
        When I enable the Armenian language
        Then I should be notified about success
        And it should be enabled
        And store should be available in the Armenian language
