@managing_routes
Feature: Routes validation
    In order to avoid making mistakes when managing a route
    As an Administrator
    I want to be prevented from adding it without specifying required fields

    Background:
        Given the store has static content "Krzysztof Krawczyk"
        And I am logged in as an administrator

    @ui
    Scenario: Trying to add a new route without specifying its name
        Given I want to add a new route
        When I choose "Krzysztof Krawczyk" as its content
        And I add it
        Then I should be notified that name is required
        And the route with content "Krzysztof Krawczyk" should not be added
