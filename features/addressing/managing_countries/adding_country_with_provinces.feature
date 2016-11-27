@managing_countries
Feature: Adding countries with provinces
    In order to sell my goods to different countries and provinces
    As an Administrator
    I want to add a new country with its provinces to the store

    Background:
        Given I am logged in as an administrator

    @ui @javascript
    Scenario: Adding a country with a province
        When I want to add a new country
        And I choose "United Kingdom"
        And I add the "Scotland" province with "GB-SCT" code
        And I add it
        Then I should be notified that it has been successfully created
        And the country "United Kingdom" should appear in the store
        And the country "United Kingdom" should have the "Scotland" province

    @ui @javascript
    Scenario: Adding a country with two provinces
        When I want to add a new country
        And I choose "United Kingdom"
        And I add the "Scotland" province with "GB-SCT" code
        And I add the "Northern Ireland" province with "GB-NIR" code and "N.Ireland" abbreviation
        And I add it
        Then I should be notified that it has been successfully created
        And the country "United Kingdom" should appear in the store
        And the country "United Kingdom" should have the "Scotland" province
        And the country "United Kingdom" should have the "Northern Ireland" province
