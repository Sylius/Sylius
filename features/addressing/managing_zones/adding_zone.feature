@addressing
Feature: Adding a new zone with country type members
    In order to apply taxes and allow shipping to geographical areas
    As an Administrator
    I want to add a new zone

    Background:
        Given I am logged in as an administrator
        And the store has country "France"
        And the store has province "Alabama"
        And the store has zone "North America"
        And the store has zone "South America"

    @todo
    Scenario: Adding zone with country type member
        Given I want to create a new zone with country members
        When I name it "European Union"
        And I specify its code as "EU"
        And I add country "France"
        And I add it
        Then I should be notified about successful creation
        And the zone "EU" with "France" country should appear in the registry

    @todo
    Scenario: Adding zone with province type member
        Given I want to create a new zone with province members
        When I name it "United States of America"
        And I specify its code as "USA"
        And I add province "Alabama"
        And I add it
        Then I should be notified about successful creation
        And the zone "United States of America" with "Alabama" province should appear in the registry

    @todo
    Scenario: Adding zone with zone type member
        Given I want to create a new zone with zone members
        When I name it "America"
        And I specify its code as "AM"
        And I add zone "North America"
        And I add zone "South America"
        And I add it
        Then I should be notified about successful creation
        And the zone "America" with "North America" and "South America" zones should appear in the registry
