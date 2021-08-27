@managing_catalog_promotions
Feature: Editing catalog promotion
    In order to change catalog promotion details
    As an Administrator
    I want to be able to edit a catalog promotion

    Background:
        Given the store operates on a single channel in "United States"
        And the store operates on a channel named "Europe"
        And there is a catalog promotion with "christmas_sale" code and "Christmas sale" name
        And I am logged in as an administrator

    @api
    Scenario: Renaming a catalog promotion
        When I rename the "Christmas sale" catalog promotion to "Black Friday"
        Then I should be notified that it has been successfully edited
        And this catalog promotion name should be "Black Friday"

    @api
    Scenario: Changing label and description of catalog promotion
        When I want to modify a catalog promotion "Christmas sale"
        And I specify its label as "Christmas -50%" in "English (United States)"
        And I describe it as "This promotion gives a 50% discount on all products" in "English (United States)"
        And I save my changes
        Then this catalog promotion label in "English (United States)" locale should be "Christmas -50%"
        And this catalog promotion description in "English (United States)" locale should be "This promotion gives a 50% discount on all products"

    @api
    Scenario: Changing availability in channels for catalog promotion
        Given the catalog promotion "Christmas sale" is available in "United States"
        When I want to modify a catalog promotion "Christmas sale"
        And I make it available in channel "Europe"
        And I make it unavailable in channel "United States"
        And I save my changes
        Then this catalog promotion should be available in channel "Europe"
        But this catalog promotion should not be available in channel "United States"

    @api
    Scenario: Trying to change code of catalog promotion
        When I try to change the code of the "Christmas sale" catalog promotion to "sale"
        Then this catalog promotion code should still be "christmas_sale"
