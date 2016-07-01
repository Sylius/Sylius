@managing_static_contents
Feature: Adding a new static content
    In order to manage content
    As an Administrator
    I want to add static content to my site

    Background:
        Given I am logged in as an administrator

    @ui
    Scenario: Adding static content
        Given I want to add a new static content
        When I set its title to "Krzysztof Krawczyk"
        And I set its internal name to "krzysztof-krawczyk"
        And I set its content to "Biography of the great Polish singer"
        And I add it
        Then I should be notified that it has been successfully created
        And the static content "Krzysztof Krawczyk" should appear in the store
