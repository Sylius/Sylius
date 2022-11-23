@managing_taxons
Feature: Deleting a taxon
    In order to remove test, obsolete or incorrect taxons
    As an Administrator
    I want to be able to delete a taxon

    Background:
        Given I am logged in as an administrator
        And the store operates on a channel named "Web Store"

    @ui @javascript
    Scenario: Deleted taxon should disappear from the registry
        Given the store classifies its products as "T-Shirts"
        When I delete taxon named "T-Shirts"
        Then the taxon named "T-Shirts" should no longer exist in the registry

    @ui @javascript
    Scenario: Deleting a taxon with a child does not delete any other taxons
        Given the store classifies its products as "Main catalog"
        And the "Main catalog" taxon has children taxon "Shoes" and "Shovels"
        And the "Shoes" taxon has children taxon "Men" and "Women"
        When I delete taxon named "Shoes"
        Then the taxon named "Shoes" should no longer exist in the registry
        And the taxon named "Men" should no longer exist in the registry
        And the taxon named "Women" should no longer exist in the registry
        But the "Shovels" taxon should appear in the registry

    @ui @javascript
    Scenario: Being unable to delete a menu taxon of a channel
        Given the store classifies its products as "T-Shirts" and "Caps"
        And channel "Web Store" has menu taxon "Caps"
        When I try to delete taxon named "Caps"
        Then I should be notified that I cannot delete a menu taxon of any channel

    @ui @javascript
    Scenario: Deleting root taxon above menu taxon
        Given the store has "Main Category" taxonomy
        And the store has "Clothes Category" taxonomy
        And channel "Web Store" has menu taxon "Main Category"
        When I want to see all taxons in store
        And I move down "Main Category" taxon
        And I delete taxon named "Clothes Category"
        Then the taxon named "Clothes Category" should no longer exist in the registry
