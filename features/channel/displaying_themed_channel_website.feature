@theme @ui
Feature: Displaying themed channel website
    In order to easily distinguish stores
    As an Visitor
    I want to see a different user interface on each one

    Background:
        Given the store operates on a single channel in "France"
        And the store has "Maverick Meerkat" theme
        And this theme changes homepage template contents to "Onions and bananas"

    Scenario: Displaying default shop homepage
        Given channel "France" does not use any theme
        When I visit this channel's homepage
        Then I should not see a homepage from "Maverick Meerkat" theme

    Scenario: Displaying themed shop homepage
        Given channel "France" uses "Maverick Meerkat" theme
        When I visit this channel's homepage
        Then I should see a homepage from that theme
