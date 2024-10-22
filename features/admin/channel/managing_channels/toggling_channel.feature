@managing_channels
Feature: Toggling a channel
    In order to change channels which are available to my customers
    As an Administrator
    I want to be able to switch state of channel between enable and disable

    Background:
        Given the store operates on a channel named "Web Channel"
        And the store operates on another channel named "Mobile Channel"
        And I am logged in as an administrator

    @api @ui
    Scenario: Disabling the channel
        Given the channel "Web Channel" is enabled
        When I want to modify this channel
        And I disable it
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this channel should be disabled

    @api @ui
    Scenario: Enabling the channel
        Given the channel "Web Channel" is disabled
        When I want to modify this channel
        And I enable it
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this channel should be enabled
