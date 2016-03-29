@addressing
Feature: Editing a zone
    In order to change the membership of areas
    As an Administrator
    I want to be able to edit a zone

    Background:
        Given I am logged in as an administrator
        And the store has a zone "United States of America" with "Alabama" and "Arizona" provinces
        And the store has a zone "European Union" with "France" and "Germany" countries
        And the store has a zone "America" with "North America" and "South America" zones

    @todo
    Scenario: Removing a province from a zone
        Given I want to modify the "United States of America" zone
        And I remove the "Arizona" member
        And I save my changes
        Then I should be notified about successful edition
        And the zone "United States of America" should have only the province "Alabama"

    @todo
    Scenario: Removing a country from a zone
        Given I want to modify the "European Union" zone
        And I remove the "Germany" member
        And I save my changes
        Then I should be notified about successful edition
        And the zone "European Union" should have only the country "France"

    @todo
    Scenario: Removing a zone from a zone
        Given I want to modify the "America" zone
        And I remove "North America" member
        And I save my changes
        Then I should be notified about successful edition
        And the zone "America" should have only the zone "South America"

    @todo
    Scenario: Renaming a zone
        Given I want to modify the "European Union" zone
        When I rename it to "EU"
        And I save my changes
        Then I should be notified about successful edition
        And this zone name should be "EU"

    @todo
    Scenario: Seeing a disabled code field when editing a zone
        When I want to modify the "European Union" zone
        Then the code field should be disabled
