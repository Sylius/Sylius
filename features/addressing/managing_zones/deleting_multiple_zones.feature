@managing_zones
Feature: Deleting multiple zones
    In order to remove test, obsolete or incorrect zones in an efficient way
    As an Administrator
    I want to be able to delete multiple zones at once

    Background:
        Given the store has zones "North America", "South America" and "Europe"
        And I am logged in as an administrator

    @ui @javascript
    Scenario: Deleting multiple zones at once
        When I browse zones
        And I check the "North America" zone
        And I check also the "South America" zone
        And I delete them
        Then I should be notified that they have been successfully deleted
        And I should see a single zone in the list
        And I should see the zone "Europe" in the list
