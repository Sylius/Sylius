@managing_zones
Feature: Re-adding zone members that have the same code
    In order to be able to edit zone's members
    As an Administrator
    I want to be able to remove and add again a zone member that has the same code

    Background:
        Given the store has country "Germany"
        And the store also has country "United Kingdom"
        And the store has a zone "Europe" with code "EP"
        And this zone has the "United Kingdom" country member
        And I am logged in as an administrator

    @api @no-ui
    Scenario: Adding a zone's member that has the same code in more than one zone
        When I want to modify the zone named "Europe"
        And I add a country "Germany"
        And I save my changes
        And I remove the "Germany" country member
        And I add the country "Germany" again
        Then I should be notified that it has been successfully edited
        And this zone should have "Germany" and "United Kingdom" country members
