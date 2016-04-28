@managing_countries
Feature: Editing country
    In order to enable or disable countries
    As an Administrator
    I want to be able to edit a country

    Background:
        Given I am logged in as an administrator

    @ui
    Scenario: Disabling country
        Given the store has country "France"
        And I want to edit this country
        When I disable it
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this country should be disabled

    @ui
    Scenario: Enabling country
        Given the store has disabled country "France"
        And I want to edit this country
        When I enable it
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this country should be enabled

    @ui
    Scenario: Seeing disabled code field while editing country
        Given the store has country "France"
        When I want to edit this country
        Then the code field should be disabled
