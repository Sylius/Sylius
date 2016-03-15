@addressing
Feature: Adding countries with provinces
    In order to sell my goods to different countries and provinces
    As an Administrator
    I want to add a new country with its provinces to the store

    Background:
        Given I am logged in as administrator

    @todo
    Scenario: Adding a country with a province
        Given I want to create a new country with a province
        When I choose "United Kingdom"
        And I add the "Scotland" province with "GB-SCT" code
        And I add it
        Then I should be notified about success
        And the country "United Kingdom" should appear in the store
        And it should have the "Scotland" province
