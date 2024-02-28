@managing_zones
Feature: Editing a zone
    In order to change my my tax and shipping configuration
    As an Administrator
    I want to be able to edit a zone

    Background:
        Given the store operates in "France" and "Germany"
        And the store also has country "United States"
        And the store also has country "Belgium"
        And this country has the "Alabama" province with "AL" code
        And this country has the "Arizona" province with "AZ" code
        And the store has a zone "North America" with code "NA"
        And the store has a zone "South America" with code "SA"
        And I am logged in as an administrator

    @ui @javascript @api
    Scenario: Removing a country from a zone
        Given the store has a zone "European Union" with code "EU"
        And it has the "France" country member
        And it also has the "Germany" country member
        When I want to modify the zone named "European Union"
        And I remove the "Germany" country member
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this zone should have only the "France" country member

    @ui @javascript @api
    Scenario: Removing and adding countries to a zone
        Given the store has a zone "European Union" with code "EU"
        And it has the "Belgium", "France" and "Germany" country members
        When I want to modify the zone named "European Union"
        And I remove the "Belgium", "France" and "Germany" country members
        And I add a country "France"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this zone should have only the "France" country member

    @ui @javascript @api
    Scenario: Removing a province from a zone
        Given the store has a zone "United States" with code "USA"
        And it has the "Alabama" province member
        And it also has the "Arizona" province member
        When I want to modify the zone named "United States"
        And I remove the "Arizona" province member
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this zone should have only the "Alabama" province member

    @ui @javascript @api
    Scenario: Removing a zone from a zone
        Given the store has a zone "America" with code "AM"
        And it has the zone named "North America"
        And it also has the zone named "South America"
        When I want to modify the zone named "America"
        And I remove the "North America" zone member
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this zone should have only the "South America" zone member

    @ui @api
    Scenario: Renaming a zone
        Given the store has a zone "European Union" with code "EU"
        And it has the "France" country member
        When I want to modify the zone named "European Union"
        And I rename it to "EU"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this zone name should be "EU"
