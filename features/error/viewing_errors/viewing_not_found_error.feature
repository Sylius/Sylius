@viewing_errors
Feature: Viewing 404 page
    In order to keep a good navigation
    As a visitor
    I need to be able to view a well 404 page when page I requested does not exist

    Background:
        Given the store operates on a single channel in "United States"

    @ui
    Scenario: Viewing not found page
        When I am on not found page
        Then I should be informed that the page does not exist
