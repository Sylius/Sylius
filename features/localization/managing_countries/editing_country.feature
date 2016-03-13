@addressing
Feature: Editing country
    In order to enable or disable countries
    As an Administrator
    I want to be able to edit country

    Background:
        Given I am logged in as administrator

    @ui
    Scenario: Disabling country
        Given the store has "France" country enabled
        And I want to edit this country
        When I disable it
        And I save my changes
        Then I should be notified about successful edition
        And this country should be disabled

    @ui
    Scenario: Enabling country
        Given the store has "France" country disabled
        And I want to edit this country
        When I enable it
        And I save my changes
        Then I should be notified about successful edition
        And this country should be enabled
