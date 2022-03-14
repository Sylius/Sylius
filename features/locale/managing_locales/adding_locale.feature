@managing_locales
Feature: Adding a new locale
    In order to provide service to my customers in their preferred language
    As an Administrator
    I want to add a new locale to the registry

    Background:
        Given I am logged in as an administrator

    @ui @api
    Scenario: Adding a new locale
        When I want to create a new locale
        And I choose Norwegian
        And I add it
        Then I should be notified that it has been successfully created
        And the store should be available in the Norwegian language
