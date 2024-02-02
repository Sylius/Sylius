@managing_taxons
Feature: Editing a taxon
    In order to change categorization of my merchandise
    As an Administrator
    I want to be able to edit a taxon

    Background:
        Given the store is available in "English (United States)"
        And the store classifies its products as "T-Shirts" and "Accessories"
        And I am logged in as an administrator

    @ui @api
    Scenario: Renaming a taxon
        When I want to modify the "T-Shirts" taxon
        And I rename it to "Stickers" in "English (United States)"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this taxon name should be "Stickers"

    @ui @api
    Scenario: Changing description
        When I want to modify the "T-Shirts" taxon
        And I change its description to "Main taxonomy for stickers" in "English (United States)"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this taxon description should be "Main taxonomy for stickers"

    @ui @javascript @api
    Scenario: Changing parent taxon
        When I want to modify the "T-Shirts" taxon
        And I change its parent taxon to "Accessories"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this taxon should belongs to "Accessories"

    @ui @api
    Scenario: Being unable to change code of taxon
        When I want to modify the "T-Shirts" taxon
        Then I should not be able to edit its code
