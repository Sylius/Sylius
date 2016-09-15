@managing_zones
Feature: Deleting a zone
    In order to remove test, obsolete or incorrect zones
    As an Administrator
    I want to be able to delete a zone

    Background:
        Given the store has a zone "North America" with code "NA"
        And the store also has a zone "South America" with code "SA"
        And the store has a tax category "Sports gear"
        And the store has "Sales Tax" tax rate of 20% for "Sports gear" within the "SA" zone
        And I am logged in as an administrator

    @ui
    Scenario: Deleted zone should disappear from the registry
        When I delete zone named "North America"
        Then I should be notified that it has been successfully deleted
        And the zone named "North America" should no longer exist in the registry

    @ui
    Scenario: Deleting zone with associated tax rates should not be possible
        When I delete zone named "South America"
        Then I should be notified that this zone cannot be deleted
        And I should still see the zone named "South America" in the list
