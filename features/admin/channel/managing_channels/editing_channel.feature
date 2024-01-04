@managing_channels
Feature: Editing channel
    In order to change channel details
    As an Administrator
    I want to be able to edit a channel

    Background:
        Given the store operates on a channel named "Web Channel"
        And I am logged in as an administrator

    @todo
    Scenario: Trying to change channel code
        When I want to modify a channel "Web Channel"
        And I change its code to "MOBILE"
        And I save my changes
        Then I should be notified that code cannot be changed
        And channel "Web Channel" should still have code "MOBILE"

    @todo @ui
    Scenario: Seeing disabled code field when editing channel
        When I want to modify a channel "Web Channel"
        Then the code field should be disabled

    @todo @ui
    Scenario: Renaming the channel
        When I want to modify a channel "Web Channel"
        And I rename it to "Website store"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this channel name should be "Website store"

    @todo @ui
    Scenario: Seeing disabled base currency field during channel edition
        When I want to modify a channel "Web Channel"
        Then the base currency field should be disabled
