@managing_static_contents
Feature: Editing a static content
    In order to change static content
    As an Administrator
    I want to be able to edit a static content

    Background:
        Given I am logged in as an administrator

    @ui
    Scenario: Change title of a static content
        Given the store has static content "Krzysztof Krawczyk" with body "Not so good singer."
        And I want to edit this static content
        When I change its body to "The best singer all over the world!"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this static content should have body "The best singer all over the world!"
