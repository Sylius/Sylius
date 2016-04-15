@managing_channels
Feature: Not being able to delete a last available channel
    In order to continuously provide access to store
    As an Administrator
    I want to be prevented from deleting last available channel

    Background:
        Given the store operates on a channel named "Web Store"
        And I am logged in as an administrator

    @ui
    Scenario: Prevented from deleting only channel
        When I delete channel "Web Store"
        Then I should be notified that it cannot be deleted
        And this channel should still be in the registry
