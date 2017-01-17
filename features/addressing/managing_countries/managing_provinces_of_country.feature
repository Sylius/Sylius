@managing_countries
Feature: Managing provinces of a country
    In order to add or remove provinces in existing countries
    As an Administrator
    I want to be able to edit a country and its provinces

    Background:
        Given the store has country "United Kingdom"
        And I am logged in as an administrator

    @ui @javascript
    Scenario: Adding a province to an existing country
        When I want to edit this country
        And I add the "Scotland" province with "GB-SCT" code
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this country should have the "Scotland" province

    @ui @javascript
    Scenario: Removing a province from an existing country
        Given this country has the "Northern Ireland" province with "GB-NIR" code
        When I want to edit this country
        And I delete the "Northern Ireland" province of this country
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this country should not have the "Northern Ireland" province

    @ui @javascript
    Scenario: Removing and adding a new province to an existing country
        Given this country has the "Northern Ireland" province with "GB-NIR" code
        When I want to edit this country
        And I delete the "Northern Ireland" province of this country
        And I add the "Scotland" province with "GB-SCT" code
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this country should not have the "Northern Ireland" province
        And this country should have the "Scotland" province
