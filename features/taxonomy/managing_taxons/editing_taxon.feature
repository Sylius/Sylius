@managing_taxons
Feature: Editing a taxon
    In order to change categorization of my merchandise
    As an Administrator
    I want to be able to edit a taxon

    Background:
        Given the store is available in "English (United States)"
        And the store classifies its products as "T-Shirts" and "Accessories"
        And I am logged in as an administrator

    @ui
    Scenario: Renaming a taxon
        Given I want to modify the "T-Shirts" taxon
        When I rename it to "Stickers" in "English (United States)"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this taxon name should be "Stickers"

    @ui
    Scenario: Changing description
        Given I want to modify the "T-Shirts" taxon
        When I rename it to "Stickers" in "English (United States)"
        And I change its description to "Main taxonomy for stickers" in "English (United States)"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this taxon description should be "Main taxonomy for stickers"

    @ui @javascript
    Scenario: Changing parent taxon
        Given I want to modify the "T-Shirts" taxon
        When I rename it to "Stickers" in "English (United States)"
        And I change its description to "Main taxonomy for stickers" in "English (United States)"
        And I set its slug to "stickers" in "English (United States)"
        And I change its parent taxon to "Accessories"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this taxon should belongs to "Accessories"

    @ui
    Scenario: Seeing a disabled code field when editing a taxon
        Given I want to modify the "T-Shirts" taxon
        Then the code field should be disabled
