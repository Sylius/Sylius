@managing_taxonomy
Feature: Adding a new taxon
    In order to categorize my merchandise
    As an Administrator
    I want to add a new taxon to the registry

    Background:
        Given the store operates on a single channel in "France"

    Scenario: Adding a new taxon
        Given I want to create a new taxon
        When I specify its code as "CATEGORY"
        And I name it "Category" in "English (United States)"
        And I add it
        Then I should be notified that it has been successfully created
        And the "Category" taxon should appear in the registry
