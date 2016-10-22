@managing_string_blocks
Feature: Deleting a string block
    In order to remove test, obsolete or incorrect string blocks
    As an Administrator
    I want to be able to delete a string block

    Background:
        Given I am logged in as an administrator

    @ui
    Scenario: Deleted taxon should disappear from the registry
        Given the store has string block "winter-sale-info"
        When I delete string block "winter-sale-info"
        Then I should be notified that it has been successfully deleted
        And the string block "winter-sale-info" should no longer exist in the store
