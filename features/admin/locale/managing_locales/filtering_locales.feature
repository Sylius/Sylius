@managing_locales
Feature: Filtering locales
    In order to quickly find the locale I need
    As an Administrator
    I want to filter available locales

    Background:
        Given the store has locale "English (United States)"
        And the store has locale "Norwegian (Norway)"
        And the store has locale "Polish (Poland)"
        And I am logged in as an administrator
        And I am browsing locales

    @todo-api @ui
    Scenario: Filtering locales by code
        When I filter by code containing "pl"
        Then I should see a single locale in the list
        And I should see the local "Polish (Poland)"
