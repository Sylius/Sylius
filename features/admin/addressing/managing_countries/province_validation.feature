@managing_countries
Feature: Province validation
    In order to avoid making mistakes when managing a province
    As an Administrator
    I want to be prevented from adding it without specifying required fields

    Background:
        Given the store has country "United Kingdom"
        And I am logged in as an administrator

    @ui @javascript @api
    Scenario: Trying to add a new province without specifying its code
        When I want to create a new province in country "United Kingdom"
        And I name the province "Scotland"
        But I do not specify the province code
        And I try to save my changes
        Then I should be notified that code is required
        And province with name "Scotland" should not be added in this country

    @ui @mink:chromedriver @api
    Scenario: Trying to add a new province with a too long code
        When I want to create a new province in country "United Kingdom"
        And I name the province "Scotland"
        And I provide a too long province code
        And I try to save my changes
        Then I should be informed that the provided province code is too long

    @ui @javascript @api
    Scenario: Trying to add a new province without specifying its name
        When I want to create a new province in country "United Kingdom"
        And I specify the province code as "GB-SCT"
        But I do not name the province
        And I try to save my changes
        Then I should be notified that name is required
        And province with code "GB-SCT" should not be added in this country

    @ui @javascript @api
    Scenario: Trying to remove name from an existing province
        Given this country has the "Northern Ireland" province with "GB-NIR" code
        When I want to edit this country
        And I remove "Northern Ireland" province name
        Then I should be notified that name of the province is required
        And the province should still be named "Northern Ireland" in this country

    @api
    Scenario: Trying to change code of existing province
        Given this country has the "Northern Ireland" province with "GB-NIR" code
        When I want to edit this country
        Then I should not be able to edit its code
