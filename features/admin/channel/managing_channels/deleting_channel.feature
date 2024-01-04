@managing_channels
Feature: Deleting a channel
    In order to remove test, obsolete or incorrect channels
    As an Administrator
    I want to be able to delete a channel

    Background:
        Given the store operates on a channel named "Web Store"
        And the store operates on another channel named "Mobile Store"
        And I am logged in as an administrator

    @ui
    Scenario: Deleted channel should disappear from the registry
        When I delete channel "Web Store"
        Then I should be notified that it has been successfully deleted
        And the "Web Store" channel should no longer exist in the registry
