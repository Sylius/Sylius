@managing_channels
Feature: Toggling a channel
    In order to change channels which are available to my customers
    As an Administrator
    I want to be able to switch state of channel between enable and disable

    Background:
        Given the store operates on a channel named "Web Channel"
        And I am logged in as an administrator

    @ui
    Scenario: Disabling the last available channel
        Given the channel "Web Channel" is enabled
        And I want to modify this channel
        When I disable it
        And I save my changes
        Then I should be notified that at least one channel has to be defined
        And channel with name "Web Channel" should still be enabled
