@managing_routes
Feature: Browsing routes
    In order to see all routes in the store
    As an Administrator
    I want to browse routes

    Background:
        Given the store has static content "Krzysztof Krawczyk"
        And the store has routes "krzysztof-krawczyk" and "the-best-musician"
        And I am logged in as an administrator

    @ui
    Scenario: Browsing routes in store
        When I want to browse routes of the store
        Then I should see 2 routes in the list
        And I should see the route "krzysztof-krawczyk" in the list
