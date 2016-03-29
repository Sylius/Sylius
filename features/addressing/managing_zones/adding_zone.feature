@managing_zones
Feature: Adding a new zone with country type members
    In order to apply taxes and allow shipping to geographical areas
    As an Administrator
    I want to add a new zone

    Background:
        Given I am logged in as an administrator
        And the store has country "France"
        And the store has a province "Alabama" with code "AL"
        And the store has a zone "North America" with code "NA"
        And the store has a zone "South America" with code "SA"

<<<<<<< HEAD
    @ui @javascript
=======
    @ui
>>>>>>> 8c44b9c... [Behat] Add province setup context
    Scenario: Adding a zone with a country type member
        Given I want to create a new zone with country members
        When I name it "European Union"
        And I specify its code as "EU"
        And I add a country "France"
        And I add it
        Then I should be notified about successful creation
        And the zone named "European Union" with the "France" country member should appear in the registry

    @ui @javascript
    Scenario: Adding a zone with province type member
        Given I want to create a new zone with province members
        When I name it "United States of America"
        And I specify its code as "USA"
        And I add a province "Alabama"
        And I add it
        Then I should be notified about successful creation
        And the zone named "United States of America" with the "Alabama" province member should appear in the registry

    @ui @javascript
    Scenario: Adding a zone with zone type member
        Given I want to create a new zone with zone members
        When I name it "America"
        And I specify its code as "AM"
        And I add a zone "North America"
        And I add a zone "South America"
        And I add it
        Then I should be notified about successful creation
        And the zone named "America" with the "North America" zone member should appear in the registry
