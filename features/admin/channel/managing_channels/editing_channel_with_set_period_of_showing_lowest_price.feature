@managing_channels
Feature: Specifying the lowest price for discounted products checking period while editing a channel
    In order to show lowest prices only from a specific period
    As an Administrator
    I want to edit a channel's lowest price for discounted products checking period

    Background:
        Given the store operates on a channel named "EU"
        And this channel has 15 days set as the lowest price for discounted products checking period
        And I am logged in as an administrator

    @no-api @ui
    Scenario: Changing the lowest price for discounted products checking period
        When I want to modify a channel "EU"
        And I specify 30 days as the lowest price for discounted products checking period
        And I save my changes
        Then I should be notified that it has been successfully edited
        And its lowest price for discounted products checking period should be set to 30 days

    @no-api @ui
    Scenario: Being prevented from changing the lowest price for discounted products checking period to zero
        When I want to modify a channel "EU"
        And I specify 0 days as the lowest price for discounted products checking period
        And I try to save my changes
        Then I should be notified that the lowest price for discounted products checking period must be greater than 0

    @no-api @ui
    Scenario: Being prevented from changing the lowest price for discounted products checking period to a negative value
        When I want to modify a channel "EU"
        And I specify -10 days as the lowest price for discounted products checking period
        And I try to save my changes
        Then I should be notified that the lowest price for discounted products checking period must be greater than 0

    @no-api @ui
    Scenario: Being prevented from changing the lowest price for discounted products checking period to a too big value
        When I want to modify a channel "EU"
        And I specify 99999999999 days as the lowest price for discounted products checking period
        And I try to save my changes
        Then I should be notified that the lowest price for discounted products checking period must be lower

    @api @no-ui
    Scenario: Changing the lowest price for discounted products checking period
        When I want to modify the price history config of channel "EU"
        And I change the lowest price for discounted products checking period to 30 days
        And I save my changes
        Then I should be notified that it has been successfully edited
        And its lowest price for discounted products checking period should be set to 30 days

    @api @no-ui
    Scenario: Being prevented from changing the lowest price for discounted products checking period to zero
        When I want to modify the price history config of channel "EU"
        And I change the lowest price for discounted products checking period to 0 days
        And I try to save my changes
        Then I should be notified that the lowest price for discounted products checking period must be greater than 0

    @api @no-ui
    Scenario: Being prevented from changing the lowest price for discounted products checking period to a negative value
        When I want to modify the price history config of channel "EU"
        And I change the lowest price for discounted products checking period to -10 days
        And I try to save my changes
        Then I should be notified that the lowest price for discounted products checking period must be greater than 0

    @api @no-ui
    Scenario: Being prevented from changing the lowest price for discounted products checking period to a too big value
        When I want to modify the price history config of channel "EU"
        And I change the lowest price for discounted products checking period to 99999999999 days
        And I try to save my changes
        Then I should be notified that the lowest price for discounted products checking period must be lower
