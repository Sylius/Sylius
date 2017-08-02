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

    @ui
    Scenario: Deleting a taxon with a child does not delete any other taxons
        Given the store classifies its products as "Main catalog"
        And the "Main catalog" taxon has children taxon "Shoes" and "Shovels"
        And the "Shoes" taxon has children taxon "Men" and "Women"
        When I delete taxon named "Shoes"
        Then the taxon named "Shoes" should no longer exist in the registry
        And the taxon named "Men" should no longer exist in the registry
        And the taxon named "Women" should no longer exist in the registry
        But the "Shovels" taxon should appear in the registry
