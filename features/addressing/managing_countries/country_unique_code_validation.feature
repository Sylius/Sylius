@managing_countries
Feature: Country unique code validation
    In order to avoid making mistakes when managing countries
    As an Administrator
    I want to be prevented from adding a new country with an existing code

    Background:
        Given the store operates in "Norway"
        And I am logged in as an administrator

    @ui
    Scenario: Trying to add a new country with used code
        When I want to add a new country
        Then I should not be able to choose "Norway"
