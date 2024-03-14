@theming
Feature: Overriding plugin template with a theme
    In order to easily distinguish store pages provided by Sylius plugin
    As an Visitor
    I want to see a different user interface on each one

    Background:
        Given the store operates on a single channel in "United States"
        And the store has "johnny/cash" theme
        And this theme changes plugin main template's content to "Ring of Fire"

    @ui @no-api
    Scenario: Displaying default plugin main template
        Given channel "United States" does not use any theme
        When I visit plugin's main page
        Then I should see a plugin's main page with content "I Walk the Line"

    @ui @no-api
    Scenario: Displaying themed plugin main template
        Given channel "United States" uses "johnny/cash" theme
        When I visit plugin's main page
        Then I should see a plugin's main page with content "Ring of Fire"
