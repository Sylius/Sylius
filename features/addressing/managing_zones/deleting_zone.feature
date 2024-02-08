@managing_zones
Feature: Deleting a zone
    In order to remove test, obsolete or incorrect zones
    As an Administrator
    I want to be able to delete a zone

    Background:
        Given the store has a zone "North America" with code "NA"
        And the store also has a zone "South America" with code "SA"
        And the store also has a zone "Central and Eastern Europe" with code "CEE"
        And the store also has a zone "Europe" with code "EU"
        And it has the zone named "Central and Eastern Europe"
        And the store has a tax category "Sports gear"
        And the store has "Sales Tax" tax rate of 20% for "Sports gear" within the "SA" zone
        And I am logged in as an administrator

    @ui @api
    Scenario: Deleted zone should disappear from the registry
        When I delete the zone named "North America"
        Then I should be notified that it has been successfully deleted
        And the zone named "North America" should no longer exist in the registry

    @ui @api
    Scenario: Deleting zone with associated tax rates should not be possible
        When I try to delete the zone named "South America"
        Then I should be notified that the zone is in use and cannot be deleted
        And I should still see the zone named "South America" in the list

    @ui @api
    Scenario: Deleting zone that is a zone member should not be possible
        When I try to delete the zone named "Central and Eastern Europe"
        Then I should be notified that this zone cannot be deleted
        And I should still see the zone named "Central and Eastern Europe" in the list
