@managing_taxons
Feature: Case-insensitive parent taxon autocomplete search
    In order to easily find parent taxons regardless of typing case
    As an Administrator
    I want to search for parent taxons in a case-insensitive manner

    Background:
        Given the store is available in "English (United States)"
        And the store classifies its products as "Category"
        And the store classifies its products as "Electronics"
        And the store classifies its products as "CLOTHING"
        And the store classifies its products as "Books & Media"
        And I am logged in as an administrator

    @ui @mink:chromedriver
    Scenario: Searching for parent taxon using lowercase letters when taxon name has uppercase letters
        When I want to create a new taxon
        And I search for "clothing" in the parent taxon autocomplete
        Then I should see "CLOTHING" in the autocomplete results

    @ui @mink:chromedriver
    Scenario: Searching for parent taxon using uppercase letters when taxon name has lowercase letters
        When I want to create a new taxon
        And I search for "ELECTRONICS" in the parent taxon autocomplete
        Then I should see "Electronics" in the autocomplete results

    @ui @mink:chromedriver
    Scenario: Searching for parent taxon using mixed case when taxon name has different case
        When I want to create a new taxon
        And I search for "books & MEDIA" in the parent taxon autocomplete
        Then I should see "Books & Media" in the autocomplete results

    @ui @mink:chromedriver
    Scenario: Searching for parent taxon with partial match case-insensitive
        When I want to create a new taxon
        And I search for "electr" in the parent taxon autocomplete
        Then I should see "Electronics" in the autocomplete results
