@managing_catalog_promotions
Feature: Editing catalog promotion
    In order to change catalog promotion details
    As an Administrator
    I want to be able to edit a catalog promotion

    Background:
        Given the store operates on a single channel in "United States"
        And there is a catalog promotion with "christmas_sale" code and "Christmas sale" name
        And I am logged in as an administrator

    @api
    Scenario: Renaming a catalog promotion
        When I rename the "Christmas sale" catalog promotion to "Black Friday"
        Then I should be notified that it has been successfully edited
        And this catalog promotion name should be "Black Friday"
