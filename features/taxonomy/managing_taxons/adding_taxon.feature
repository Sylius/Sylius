@managing_taxons
Feature: Adding a new taxon
    In order to categorize my merchandise
    As an Administrator
    I want to add a new taxon to the registry

    Background:
        Given the store is available in "English (United States)"
        And I am logged in as an administrator

    @ui
    Scenario: Adding a new taxon
        Given I want to create a new taxon
        When I specify its code as "t-shirts"
        And I name it "T-Shirts" in "English (United States)"
        And I set its slug to "t-shirts" in "English (United States)"
        And I add it
        Then I should be notified that it has been successfully created
        And the "T-Shirts" taxon should appear in the registry

    @ui
    Scenario: Adding a new taxon with slug and description
        Given I want to create a new taxon
        When I specify its code as "category"
        And I name it "Category" in "English (United States)"
        And I set its slug to "category" in "English (United States)"
        And I describe it as "Main taxonomy for products." in "English (United States)"
        And I add it
        Then I should be notified that it has been successfully created
        And the "Category" taxon should appear in the registry
        And it should not belong to any other taxon
