@managing_taxons
Feature: Toggle the taxon
    In order to associate products to a taxon while it isn't published to customers
    As an Administrator
    I want to create a disabled taxon and toggle the taxon later

    Background:
        Given the store is available in "English (United States)"
        And the store classifies its products as "T-Shirts" and "Accessories"
        And I am logged in as an administrator

    @ui
    Scenario: Adding a disabled taxon
        Given I want to create a new taxon
        When I specify its code as "jeans"
        And I name it "Jeans" in "English (United States)"
        And I set its slug to "jeans" in "English (United States)"
        And I disable it
        And I add it
        Then I should be notified that it has been successfully created
        And the "Jeans" taxon should appear in the registry
        And it should be marked as disabled
