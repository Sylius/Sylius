@managing_taxons
Feature: Deleting a taxon
    In order to remove test, obsolete or incorrect taxons
    As an Administrator
    I want to be able to delete a taxon

    Background:
        Given the store operates on a single channel in "France"
        And the store classifies its products as "T-Shirts"
        And I am logged in as an administrator

    @ui
    Scenario: Deleted taxon should disappear from the registry
        When I delete taxon named "T-Shirts"
        Then I should be notified that it has been successfully deleted
        And the taxon named "T-Shirts" should no longer exist in the registry
