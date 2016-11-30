@managing_zones
Feature: Zone unique code validation
    In order to uniquely identify zones
    As an Administrator
    I want to be prevented from adding two zones with same code

    Background:
        Given the store has country "United States"
        And the store has a zone "United States" with code "US"
        And this zone has the "United States" country member
        And I am logged in as an administrator

    @ui
    Scenario: Trying to add zone with taken code
        When I want to create a new zone consisting of country
        And I specify its code as "US"
        And I name it "United States"
        And I try to add it
        Then I should be notified that zone with this code already exists
        And there should still be only one zone with code "US"
