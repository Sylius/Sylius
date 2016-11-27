@managing_zones
Feature: Editing a zone
    In order to change my my tax and shipping configuration
    As an Administrator
    I want to be able to edit a zone

    Background:
        Given the store operates in "France" and "Germany"
        And the store also has country "United States"
        And this country has the "Alabama" province with "AL" code
        And this country has the "Arizona" province with "AZ" code
        And the store has a zone "North America" with code "NA"
        And the store has a zone "South America" with code "SA"
        And I am logged in as an administrator

    @ui @javascript
    Scenario: Removing a country from a zone
        Given the store has a zone "European Union" with code "EU"
        And it has the "France" country member
        And it also has the "Germany" country member
        When I want to modify the zone named "European Union"
        And I remove the "Germany" country member
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this zone should have only the "France" country member

    @ui @javascript
    Scenario: Removing a province from a zone
        Given the store has a zone "United States" with code "USA"
        And it has the "Alabama" province member
        And it also has the "Arizona" province member
        When I want to modify the zone named "United States"
        And I remove the "Arizona" province member
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this zone should have only the "Alabama" province member

    @ui @javascript
    Scenario: Removing a zone from a zone
        Given the store has a zone "America" with code "AM"
        And it has the zone named "North America"
        And it also has the zone named "South America"
        When I want to modify the zone named "America"
        And I remove the "North America" zone member
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this zone should have only the "South America" zone member

    @ui
    Scenario: Renaming a zone
        Given the store has a zone "European Union" with code "EU"
        And it has the "France" country member
        When I want to modify the zone named "European Union"
        And I rename it to "EU"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this zone name should be "EU"

    @ui
    Scenario: Seeing a disabled code field when editing a zone
        Given the store has a zone "European Union" with code "EU"
        When I want to modify the zone named "European Union"
        Then the code field should be disabled
