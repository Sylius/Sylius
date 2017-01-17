@managing_zones
Feature: Adding a new zone with country type members
    In order to apply taxes and allow shipping to geographical areas
    As an Administrator
    I want to add a new zone

    Background:
        Given the store has country "France"
        And the store also has country "United States"
        And this country has the "Alabama" province with "AL" code
        And the store has a zone "North America" with code "NA"
        And I am logged in as an administrator

    @ui @javascript
    Scenario: Adding a zone with a country type member
        When I want to create a new zone consisting of country
        And I name it "European Union"
        And I specify its code as "EU"
        And I add a country "France"
        And I add it
        Then I should be notified that it has been successfully created
        And the zone named "European Union" with the "France" country member should appear in the registry

    @ui @javascript
    Scenario: Adding a zone with province type member
        When I want to create a new zone consisting of province
        And I name it "United States"
        And I specify its code as "USA"
        And I add a province "Alabama"
        And I add it
        Then I should be notified that it has been successfully created
        And the zone named "United States" with the "Alabama" province member should appear in the registry

    @ui @javascript
    Scenario: Adding a zone with zone type member
        When I want to create a new zone consisting of zone
        And I name it "America"
        And I specify its code as "AM"
        And I add a zone "North America"
        And I add it
        Then I should be notified that it has been successfully created
        And the zone named "America" with the "North America" zone member should appear in the registry

    @ui @javascript
    Scenario: Adding a zone with a country type member and a shipping scope
        When I want to create a new zone consisting of country
        And I name it "European Union"
        And I specify its code as "EU"
        And I add a country "France"
        And I select its scope as "shipping"
        And I add it
        Then I should be notified that it has been successfully created
        And the zone named "European Union" with the "France" country member should appear in the registry
        And its scope should be "shipping"
