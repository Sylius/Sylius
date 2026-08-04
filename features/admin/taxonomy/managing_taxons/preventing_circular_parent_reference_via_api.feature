@managing_taxons
Feature: Preventing circular parent reference via API when updating a taxon
    In order to maintain a valid taxon tree structure
    As an Administrator
    I want to be prevented from setting a taxon or its descendants as its parent via API

    Background:
        Given the store is available in "English (United States)"
        And the store has "Category" taxonomy
        And the "Category" taxon has children taxon "Clothing" and "Books"
        And the "Clothing" taxon has children taxon "T-Shirts" and "Jeans"
        And the "T-Shirts" taxon has children taxon "Men" and "Women"
        And I am logged in as an administrator

    @api @no-ui
    Scenario: Trying to set a child taxon as parent
        When I want to modify the "Clothing" taxon
        And I try to change its parent taxon to "T-Shirts"
        And I try to save my changes
        Then I should be notified that the parent relation is invalid
        And this taxon should still belong to "Category"

    @api @no-ui
    Scenario: Trying to set a grandchild taxon as parent
        When I want to modify the "Clothing" taxon
        And I try to change its parent taxon to "Men"
        And I try to save my changes
        Then I should be notified that the parent relation is invalid
        And this taxon should still belong to "Category"

    @api @no-ui
    Scenario: Successfully changing parent to a valid taxon
        When I want to modify the "Clothing" taxon
        And I change its parent taxon to "Books"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this taxon should belong to "Books"

    @api @no-ui
    Scenario: Successfully setting a sibling as parent
        When I want to modify the "T-Shirts" taxon
        And I change its parent taxon to "Jeans"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this taxon should belong to "Jeans"
