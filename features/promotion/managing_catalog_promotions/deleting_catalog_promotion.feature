@managing_catalog_promotions
Feature: Deleting a catalog promotion
    In order to remove obsolete catalog promotion
    As an Administrator
    I want to be able to delete a catalog promotion

    Background:
        Given the store operates on a single channel in "United States"
        And there is a catalog promotion "Christmas sale"
        And I am logged in as an administrator

    @api
    Scenario: Deleting a catalog promotion
        When I delete a "Christmas sale" catalog promotion
        Then I should be notified that it has been successfully deleted
        And this catalog promotion should no longer exist in the registry
