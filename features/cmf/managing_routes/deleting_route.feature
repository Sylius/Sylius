@managing_routes
Feature: Deleting a route
    In order to remove test, obsolete or incorrect routes
    As an Administrator
    I want to be able to delete a route

    Background:
        Given the store has static content "Krzysztof Krawczyk"
        And I am logged in as an administrator

    @ui @todo
    Scenario: Deleted taxon should disappear from the registry
        Given the store has route "krzysztof-krawczyk"
        When I delete route "krzysztof-krawczyk"
        Then I should be notified that it has been successfully deleted
        And the route "krzysztof-krawczyk" should no longer exist in the registry
