@theme
Feature: Managing themes per channel
    In order to allow customizing channels' appearance
    As a store owner
    I want to be able to set theme per channel

    Background:
        Given the store operates on a channel named "France"
        And the store has "Maverick Meerkat" theme
        And I am logged in as administrator

    @ui
    Scenario: None of the themes are used by default
        When I create a new channel "Poland"
        Then that channel should not use any theme

    @ui
    Scenario: Setting a theme on a channel
        When I set "France" channel theme to "Maverick Meerkat"
        Then that channel should use that theme

    @ui
    Scenario: Unsetting a channel theme
        Given channel "France" uses "Maverick Meerkat" theme
        When I unset theme on that channel
        Then that channel should not use any theme
