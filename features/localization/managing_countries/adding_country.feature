@addressing
Feature: Adding country
    In order to sell my goods to different countries
    As an Administrator
    I want to add new country to the store

    Background:
        Given I am logged in as administrator

    @ui
    Scenario: Adding new country
        Given I want to create new country
        When I name it "France"
        And I add it
        Then I should be notified about success
        And this country should appear in the store
