@managing_locales
Feature: Channel independent translatable entities locales
    In order to translate my application properly
    As an Administrator
    I should be able to translate an entity in all locales, not just the channel ones

    Background:
        Given the store operates on a channel named "Web"
        And that channel allows to shop using the "English (United States)" locale
        And it uses the "English (United States)" locale by default
        And I am logged in as an administrator

    @ui
    Scenario: Using all locales to translate an entity
        Given the store has locale "German (Germany)"
        When I want to create a new translatable entity
        Then I should be able to translate it in "English (United States)"
        And I should be able to translate it in "German (Germany)"
