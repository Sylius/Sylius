@managing_zones
Feature: Zone validation
    In order to avoid making mistakes when managing a zone
    As an Administrator
    I want to be prevented from adding it without specifying required fields

    Background:
        Given the store operates in "France" and "Germany"
        And the store has country "United States"
        And I am logged in as an administrator

    @ui @api
    Scenario: Trying to add a zone without specifying its code
        When I want to create a new zone consisting of country
        And I name it "European Union"
        But I do not specify its code
        And I try to add it
        Then I should be notified that code is required
        And zone with name "European Union" should not be added

    @ui @api
    Scenario: Trying to add a zone with a too long code
        When I want to create a new zone consisting of country
        And I name it "European Union"
        And I specify a too long code
        And I try to add it
        Then I should be notified that code is too long

    @ui @api
    Scenario: Trying to add a zone without specifying its name
        When I want to create a new zone consisting of country
        And I specify its code as "EU"
        But I do not specify its name
        And I try to add it
        Then I should be notified that name is required
        And zone with code "EU" should not be added

    @ui @api
    Scenario: Trying to add a zone without any countries
        When I want to create a new zone consisting of country
        And I name it "European Union"
        And I specify its code as "EU"
        But I do not add a country member
        And I add it
        Then I should be notified that at least one zone member is required
        And zone with name "European Union" should not be added

    @ui @api
    Scenario: Being unable to edit code of an existing zone
        Given the store has a zone "European Union" with code "EU"
        And it has the "France" country member
        When I want to modify the zone named "European Union"
        Then I should not be able to edit its code

    @ui @api
    Scenario: Being unable to add itself to members during editing an existing zone
        Given the store has a zone "European Union" with code "EU"
        When I want to modify the zone named "European Union"
        Then I can not add a zone "European Union"

    @ui
    Scenario: Seeing a disabled type field when adding country type zone
        When I want to create a new zone consisting of country
        Then I should not be able to edit its type
        And it should be of country type

    @ui
    Scenario: Seeing a disabled type field when adding province type zone
        When I want to create a new zone consisting of province
        Then I should not be able to edit its type
        And it should be of province type
