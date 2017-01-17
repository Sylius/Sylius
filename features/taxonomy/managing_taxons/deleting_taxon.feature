@managing_taxons
Feature: Deleting a taxon
    In order to remove test, obsolete or incorrect taxons
    As an Administrator
    I want to be able to delete a taxon

    Background:
        Given I am logged in as an administrator

    @ui
    Scenario: Deleted taxon should disappear from the registry
        Given the store classifies its products as "T-Shirts"
        When I delete taxon named "T-Shirts"
        Then the taxon named "T-Shirts" should no longer exist in the registry
