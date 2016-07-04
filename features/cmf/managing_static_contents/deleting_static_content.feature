@managing_static_contents
Feature: Deleting a static content
    In order to remove test, obsolete or incorrect static contents
    As an Administrator
    I want to be able to delete a static content

    Background:
        Given I am logged in as an administrator

    @ui
    Scenario: Deleted taxon should disappear from the registry
        Given the store has static content "Krzysztof Krawczyk"
        When I delete static content "Krzysztof Krawczyk"
        Then I should be notified that it has been successfully deleted
        And the static content "Krzysztof Krawczyk" should no longer exist in the registry
