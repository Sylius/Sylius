@theming
Feature: Displaying themed channel website
    In order to easily distinguish stores
    As an Visitor
    I want to see a different user interface on each one

    Background:
        Given the store operates on a single channel in "United States"
        And the store has "maverick/meerkat" theme
        And this theme changes homepage template contents to "Onions and bananas"

    @ui
    Scenario: Displaying default shop homepage
        Given channel "United States" does not use any theme
        When I visit this channel's homepage
        Then I should not see a homepage from "maverick/meerkat" theme

    @ui
    Scenario: Displaying themed shop homepage
        Given channel "United States" uses "maverick/meerkat" theme
        When I visit this channel's homepage
        Then I should see a homepage from that theme
