@theming
Feature: Managing themes per channel
    In order to allow customizing channels' appearance
    As a store owner
    I want to be able to set theme per channel

    Background:
        Given the store operates on a channel named "United States"
        And the store has "maverick/meerkat" theme
        And I am logged in as an administrator

    @ui
    Scenario: None of the themes are used by default
        When I create a new channel "Poland"
        Then that channel should not use any theme

    @ui
    Scenario: Setting a theme on a channel
        When I set "United States" channel theme to "maverick/meerkat"
        Then that channel should use that theme

    @ui
    Scenario: Unsetting a channel theme
        Given channel "United States" uses "maverick/meerkat" theme
        When I unset theme on that channel
        Then that channel should not use any theme
