@theme
Feature: Using themes
    In order to allow customizing channels' appearance
    As a store owner
    I want to be able to set theme per channel

    Background:
        Given the store operates on a channel named "France"
        And there are "Maverick Meerkat" and "Vivid Vervet" themes defined
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
        Given "France" channel is using "Maverick Meerkat" theme
        When I unset theme on that channel
        Then that channel should not use any theme

    @todo @ui
    Scenario: Every channel should use different theme
        Given the store operates on another channel named "Poland"
        When I set "Maverick Meerkat" theme to be used by "France" channel
        And I set "Vivid Vervet" theme to be used by "Poland" channel
        Then "France" channel should use "Maverick Meerkat" theme
        And "Poland" channel should use "Vivid Vervet" theme
