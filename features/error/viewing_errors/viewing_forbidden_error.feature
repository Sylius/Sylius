@viewing_errors
Feature: Viewing forbidden page
    In order to keep a good navigation
    As a visitor
    I need to be able to view a well forbidden page when page I requested is forbidden

    Background:
        Given the store operates on a single channel in "United States"

    @ui
    Scenario: Viewing forbidden page
        When I am on forbidden page
        Then I should see the title "Unexpected error occurred."
