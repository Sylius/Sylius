@managing_catalog_promotions
Feature: Editing promotion
    In order to change promotion details
    As an Administrator
    I want to be able to edit a promotion

    Background:
        Given the store operates on a single channel in "United States"
        And there is a catalog promotion "Christmas sale"
        And I am logged in as an administrator

    @api
    Scenario: Renaming a catalog promotion
        Given I want to modify the "Christmas sale" catalog promotion
        When I rename it to "Black Friday"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this catalog promotion name should be "Black Friday"
