@managing_zones
Feature: Editing a zone
    In order to change my my tax and shipping configuration
    As an Administrator
    I want to be able to edit a zone

    Background:
        Given the store also has country "France"
        And the store has a zone "North America" with code "NA" and priority 2
        And the store has a zone "South America" with code "SA" and priority 1
        And the store has a zone "Australia" with code "AU" and priority 0
        And I am logged in as an administrator

    @api @ui
    Scenario: Zones are sorted by priority in descending order by default
        When I want to see all zones in store
        Then I should see 3 zones in the list
        Then the first zone on the list should have name "North America"
        Then the last zone on the list should have name "Australia"

    @api @ui
    Scenario: Zone's default priority is 0 which puts it at the bottom of the list
        Given the store has a zone "European Union" with code "EU"
        When I want to see all zones in store
        Then I should see 4 zones in the list
        And the last zone on the list should have name "European Union"
