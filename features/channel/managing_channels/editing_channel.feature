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
        Given I want to modify a channel "Web Channel"
        When I change its code to "MOBILE"
        And I save my changes
        Then I should be notified that code cannot be changed
        And channel "Web Channel" should still have code "MOBILE"

    @ui
    Scenario: Seeing disabled code field when editing channel
        When I want to modify a channel "Web Channel"
        Then the code field should be disabled

    @ui
    Scenario: Renaming the channel
        Given I want to modify a channel "Web Channel"
        When I rename it to "Website store"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this channel name should be "Website store"

    @ui
    Scenario: Seeing disabled base currency field during channel edition
        When I want to modify a channel "Web Channel"
        Then the base currency field should be disabled
