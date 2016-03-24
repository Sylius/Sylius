@addressing
Feature: Editing a zone
    In order to change membership of areas
    As an Administrator
    I want to be able to edit a zone

    Background:
        Given I am logged in as an administrator
        And the store has zone "United States of America" with "Alabama" and "Arizona" provinces
        And the store has zone "European Union" with "France" and "Germany" countries
        And the store has zone "America" with "North America" and "South America" zones

    @todo
    Scenario: Removing province from zone
        Given I want to modify "United States of America"
        And I remove "Arizona" member
        And I save my changes
        Then I should be notified about successful edition
        And the zone "United States of America" should have only "Alabama" province

    @todo
    Scenario: Removing country from zone
        Given I want to modify "European Union"
        And I remove "Germany" member
        And I save my changes
        Then I should be notified about successful edition
        And the zone "European Union" should have only "France" country

    @todo
    Scenario: Removing zone from zone
        Given I want to modify "America"
        And I remove "North America" member
        And I save my changes
        Then I should be notified about successful edition
        And the zone "America" should have only "South America" zone

    @todo
    Scenario: Renaming zone
        Given I want to modify "European Union"
        When I rename it to "EU"
        And I save my changes
        Then I should be notified about successful edition
        And this zone name should be "EU"

    @todo
    Scenario: Seeing disabled code field when editing zone
        When I want to modify "European Union"
        Then the code field should be disabled
