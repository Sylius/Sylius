@promotion
Feature: Deleting a promotion
    In order to remove test, obsolete or incorrect promotions
    As an Administrator
    I want to be able to remove promotions

    Background:
        Given the store operates on a single channel in "France"
        And there is a promotion "Holiday promotion"

    @todo
    Scenario: Deleted promotion should disappear from the registry
        When I delete promotion "Holiday promotion"
        Then it should not exist in the registry
