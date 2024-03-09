@managing_taxons
Feature: Toggling the taxon
    In order to associate products to a taxon while it isn't published to customers
    As an Administrator
    I want to create a disabled taxon and toggle the taxon later

    Background:
        Given the store is available in "English (United States)"
        And the store classifies its products as "T-Shirts" and "Accessories"
        And I am logged in as an administrator

    @ui @api
    Scenario: Adding a disabled taxon
        When I want to create a new taxon
        And I specify its code as "jeans"
        And I name it "Jeans" in "English (United States)"
        And I set its slug to "jeans" in "English (United States)"
        And I disable it
        And I add it
        Then I should be notified that it has been successfully created
        And the "Jeans" taxon should appear in the registry
        And it should be disabled

    @ui @api
    Scenario: Enabling a Taxon
        Given the "T-Shirts" taxon is disabled
        When I want to modify the "T-Shirts" taxon
        And I enable it
        And I save my changes
        Then I should be notified that it has been successfully edited
        And it should be enabled

    @ui @api
    Scenario: Disabling a Taxon
        Given the "T-Shirts" taxon is enabled
        When I want to modify the "T-Shirts" taxon
        And I disable it
        And I save my changes
        Then I should be notified that it has been successfully edited
        And it should be disabled
