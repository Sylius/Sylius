@managing_channels
Feature: Browsing channels
    In order to have a overview of all defined channels
    As an Administrator
    I want to be able to browse list of them

    Background:
        Given the store operates on a channel named "Web Channel" in "USD" currency
        And the store operates on another channel named "Mobile Channel"
        And I am logged in as an administrator

    @ui
    Scenario: Browsing defined channels
        When I want to browse channels
        Then I should see 2 channels in the list
        And the channel "Web Channel" should be in the registry
