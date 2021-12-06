@managing_countries
Feature: Editing country
    In order to enable or disable countries
    As an Administrator
    I want to be able to edit a country

    Background:
        Given I am logged in as an administrator

    @ui @api
    Scenario: Disabling country
        Given the store has country "United States"
        When I want to edit this country
        And I disable it
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this country should be disabled

    @ui @api
    Scenario: Enabling country
        Given the store has disabled country "United States"
        When I want to edit this country
        And I enable it
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this country should be enabled

    @ui @api
    Scenario: Seeing disabled code field while editing country
        Given the store has country "United States"
        When I want to edit this country
        Then I should not be able to edit its code
