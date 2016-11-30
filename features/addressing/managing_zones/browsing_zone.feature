@managing_zones
Feature: Browsing zones
    In order to see all zones in the store
    As an Administrator
    I want to browse zones

    Background:
        Given the store has a zone "North America" with code "NA"
        And the store also has a zone "South America" with code "SA"
        And I am logged in as an administrator

    @ui
    Scenario: Browsing zones in store
        When I want to see all zones in store
        Then I should see 2 zones in the list
        And I should see the zone named "North America" in the list
