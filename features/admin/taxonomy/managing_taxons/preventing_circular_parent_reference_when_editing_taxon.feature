@managing_taxons
Feature: Preventing circular parent reference when editing a taxon
    In order to maintain a valid taxon tree structure
    As an Administrator
    I want to be prevented from setting a taxon or its descendants as its parent

    Background:
        Given the store is available in "English (United States)"
        And the store has "Category" taxonomy
        And the "Category" taxon has children taxon "Clothing" and "Books"
        And the "Clothing" taxon has children taxon "T-Shirts" and "Jeans"
        And the "T-Shirts" taxon has children taxon "Men" and "Women"
        And I am logged in as an administrator

    @no-api @ui @mink:chromedriver
    Scenario: A taxon cannot be set as its own parent
        When I want to modify the "Clothing" taxon
        And I try to search for "Clothing" in the parent taxon autocomplete
        Then I should not see "Clothing" in the parent taxon autocomplete results

    @no-api @ui @mink:chromedriver
    Scenario: A child taxon cannot be set as parent of its ancestor
        When I want to modify the "Clothing" taxon
        And I try to search for "T-Shirts" in the parent taxon autocomplete
        Then I should not see "T-Shirts" in the parent taxon autocomplete results

    @no-api @ui @mink:chromedriver
    Scenario: A grandchild taxon cannot be set as parent of its ancestor
        When I want to modify the "Clothing" taxon
        And I try to search for "Men" in the parent taxon autocomplete
        Then I should not see "Men" in the parent taxon autocomplete results

    @no-api @ui @mink:chromedriver
    Scenario: All descendants are excluded from parent selection
        When I want to modify the "Clothing" taxon
        And I try to search for "Jeans" in the parent taxon autocomplete
        Then I should not see "Jeans" in the parent taxon autocomplete results

    @no-api @ui @mink:chromedriver
    Scenario: Sibling taxons can be selected as parent
        When I want to modify the "Clothing" taxon
        And I try to search for "Books" in the parent taxon autocomplete
        Then I should see "Books" in the parent taxon autocomplete results

    @no-api @ui @mink:chromedriver
    Scenario: Taxons from different branches can be selected as parent
        When I want to modify the "Clothing" taxon
        And I try to search for "Category" in the parent taxon autocomplete
        Then I should see "Category" in the parent taxon autocomplete results

    @no-api @ui @mink:chromedriver
    Scenario: A leaf taxon excludes only itself from parent selection
        When I want to modify the "Men" taxon
        And I try to search for "Men" in the parent taxon autocomplete
        Then I should not see "Men" in the parent taxon autocomplete results
        And I try to search for "Women" in the parent taxon autocomplete
        And I should see "Women" in the parent taxon autocomplete results
        And I try to search for "T-Shirts" in the parent taxon autocomplete
        And I should see "T-Shirts" in the parent taxon autocomplete results

    @no-api @ui @mink:chromedriver
    Scenario: Successfully changing parent to a valid taxon (not itself or descendant)
        When I want to modify the "Clothing" taxon
        And I change its parent taxon to "Books"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this taxon should belong to "Books"
