@theme @ui
Feature: Displaying themed channel website
    In order to allow customizing channels' appearance
    As an Administrator
    I want to be able to set theme per channel

    Background:
        Given the store operates on a channel named "France"
        And the store has "Maverick Meerkat" theme
        And this theme changes homepage template contents to "Onions and bananas"

    @todo
    Scenario: Displaying default shop homepage
        Given channel "France" does not use any theme
        When I visit this channel's homepage
        Then I should not see "Onions and bananas"

    @todo
    Scenario: Displaying themed shop homepage
        Given channel "France" uses "Maverick Meerkat" theme
        When I visit this channel's homepage
        Then I should see "Onions and bananas"
