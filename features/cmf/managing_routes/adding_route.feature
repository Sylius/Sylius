@managing_routes
Feature: Adding a new route
    In order to make my content available for customers
    As an Administrator
    I want to add route to my site

    Background:
        Given the store has static content "Krzysztof Krawczyk"
        And I am logged in as an administrator

    @ui
    Scenario: Adding a route
        Given I want to add a new route
        When I set its name to "krzysztof-krawczyk"
        And I choose "Krzysztof Krawczyk" as its content
        And I add it
        Then I should be notified that it has been successfully created
        And the route "krzysztof-krawczyk" should appear in the store
