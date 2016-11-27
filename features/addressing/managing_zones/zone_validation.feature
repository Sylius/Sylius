@managing_zones
Feature: Zone validation
    In order to avoid making mistakes when managing a zone
    As an Administrator
    I want to be prevented from adding it without specifying required fields

    Background:
        Given the store has country "United States"
        And I am logged in as an administrator

    @ui
    Scenario: Trying to add a zone without specifying its code
        When I want to create a new zone consisting of country
        And I name it "European Union"
        But I do not specify its code
        And I try to add it
        Then I should be notified that code is required
        And zone with code "European Union" should not be added

    @ui
    Scenario: Trying to add a zone without specifying its name
        When I want to create a new zone consisting of country
        And I specify its code as "EU"
        But I do not specify its name
        And I try to add it
        Then I should be notified that name is required
        And zone with code "EU" should not be added

    @ui
    Scenario: Trying to add a zone without any countries
        When I want to create a new zone consisting of country
        And I name it "European Union"
        And I specify its code as "EU"
        But I do not add a country member
        And I add it
        Then I should be notified that at least one zone member is required
        And zone with name "European Union" should not be added

    @ui
    Scenario: Seeing a disabled type field when adding country type zone
        When I want to create a new zone consisting of country
        Then the type field should be disabled
        And it should be of country type

    @ui
    Scenario: Seeing a disabled type field when adding province type zone
        When I want to create a new zone consisting of province
        Then the type field should be disabled
        And it should be of province type
