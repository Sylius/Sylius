@managing_channels
Feature: Editing channel
    In order to change channel details
    As an Administrator
    I want to be able to edit a channel

    Background:
        Given the store operates on a channel named "Web Channel"
        And I am logged in as an administrator

    @api @ui
    Scenario: Being unable to change the code of an existing channel
        When I want to modify a channel "Web Channel"
        Then I should not be able to edit its code

    @api @ui
    Scenario: Renaming the channel
        When I want to modify a channel "Web Channel"
        And I rename it to "Website store"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this channel name should be "Website store"

    @api @ui
    Scenario: Being unable to change base currency of an existing channel
        When I want to modify a channel "Web Channel"
        Then I should not be able to edit its base currency
