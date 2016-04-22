@managing_promotions
Feature: Deleting a promotion
    In order to remove test, obsolete or incorrect promotions
    As an Administrator
    I want to be able to delete a promotion from the registry

    Background:
        Given the store operates on a single channel in "France"
        And there is a promotion "Christmas sale"

    @domain
    Scenario: Deleted promotion should disappear from the registry
        When I delete promotion "Christmas sale"
        Then I should be notified that it has been successfully deleted
        And this promotion should no longer exist in the promotion registry
