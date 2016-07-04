@managing_routes
Feature: Editing a route
    In order to change route
    As an Administrator
    I want to be able to edit a route

    Background:
        Given the store has static contents "Krzysztof Krawczyk" and "Ryszard Rynkowski"
        And I am logged in as an administrator

    @ui @todo
    Scenario: Change title of a route
        Given the store has route "krzysztof-krawczyk" with "Ryszard Rynkowski" as its content
        And I want to edit this route
        When I change its content to "Krzysztof Krawczyk"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this route should have assigned "Krzysztof Krawczyk" content
