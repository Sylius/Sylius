@managing_zones
Feature: Deleting a zone
    In order to remove test, obsolete or incorrect zones
    As an Administrator
    I want to be able to delete a zone

    Background:
        Given the store has a zone "North America" with code "NA"
        And the store also has a zone "South America" with code "SA"
        And I am logged in as an administrator

    @ui
    Scenario: Deleted zone should disappear from the registry
        When I delete zone named "North America"
        Then I should be notified that it has been successfully deleted
        And the zone named "North America" should no longer exist in the registry
