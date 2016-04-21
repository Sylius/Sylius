@managing_zones
Feature: Zone unique code validation
    In order to uniquely identify zones
    As an Administrator
    I want to be prevented from adding two zones with same code

    Background:
        Given the store has country "France"
        And the store has a zone "European Union" with code "EU"
        And this zone has the "France" country member
        And I am logged in as an administrator

    @ui
    Scenario: Trying to add zone with taken code
        Given I want to create a new zone consisting of country
        When I specify its code as "EU"
        And I name it "European Union"
        And I try to add it
        Then I should be notified that zone with this code already exists
        And there should still be only one zone with code "EU"
