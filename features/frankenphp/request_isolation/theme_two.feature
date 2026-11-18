@worker @worker:theme @mink:workerphp
Feature: A scenario renders its own theme, not the one left by the scenario before it

    Each scenario declares a different theme on purpose. theme.feature cannot
    tell a stale theme from a fresh one, because both of its scenarios use the
    same theme name with the same contents.

    @no-api @ui
    Scenario: The first scenario renders its own theme
        Given the store operates on a channel named "Poland"
        And the store has "alpha/one" theme
        And this theme changes homepage template contents to "ALPHA ONE"
        And channel "Poland" uses "alpha/one" theme
        When I browse the "Poland" channel
        Then I should see a homepage from "alpha/one" theme

    @no-api @ui
    Scenario: The next scenario renders its own theme as well
        Given the store operates on a channel named "Poland"
        And the store has "beta/two" theme
        And this theme changes homepage template contents to "BETA TWO"
        And channel "Poland" uses "beta/two" theme
        When I browse the "Poland" channel
        Then I should see a homepage from "beta/two" theme
