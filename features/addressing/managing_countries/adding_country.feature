@managing_countries
Feature: Adding a new country
    In order to sell my goods to different countries
    As an Administrator
    I want to add a new country to the store

    Background:
        Given I am logged in as an administrator

    @ui
    Scenario: Adding country
        Given I want to add a new country
        When I choose "France"
        And I add it
        Then I should be notified that it has been successfully created
        And the country "France" should appear in the store
