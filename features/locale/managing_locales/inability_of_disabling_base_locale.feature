@managing_locales
Feature: Inability of disabling the base locale
    In order to always have the base locale available in the store
    As an Administrator
    I want to be prevented from disabling the base locale

    Background:
        Given I am logged in as an administrator

    @ui
    Scenario:
        Given the store operates on a single channel
        And it uses the "English (United States)" locale by default
        And I want to edit this locale
        Then I should not be able to disable this locale
