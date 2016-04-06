@@addressing
Feature: Province validation
    In order to avoid making mistakes when managing a province
    As an Administrator
    I want to be prevented from adding it without specifying required fields

    Background:
        Given the store has country "United Kingdom"
        And I am logged in as an administrator

    @todo
    Scenario: Trying to add a new province without specifying its code
        Given I want to create a new province in country "United Kingdom"
        When I name the province "Scotland"
        But I do not specify the province code
        And I try to save changes
        Then I should be notified that code is required
        And province with name "Scotland" should not be added

    @todo
    Scenario: Trying to add a new province without specifying its name
        Given I want to create a new province in country "United Kingdom"
        When I specify the province code as "GB-SCT"
        But I do not name the province
        And I try to save changes
        Then I should be notified that name is required
        And province with code "GB-SCT" should not be added

    @todo
    Scenario: Trying to remove name from an existing province
        Given this country has the "Northern Ireland" province with "GB-NIR" code
        And I want to edit this country
        When I remove "Northern Ireland" province name
        And I try to save my changes
        Then I should be notified that name is required
        And this province should still be named "Northern Ireland"
