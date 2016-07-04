@managing_static_contents
Feature: Static contents validation
    In order to avoid making mistakes when managing a static content
    As an Administrator
    I want to be prevented from adding it without specifying required fields

    Background:
        Given I am logged in as an administrator

    @ui
    Scenario: Trying to add a new static content without specifying its body
        Given I want to add a new static content
        When I set its title to "Krzysztof Krawczyk"
        And I set its name to "krzysztof-krawczyk"
        And I add it
        Then I should be notified that body is required
        And the static content "Krzysztof Krawczyk" should not be added

    @ui
    Scenario: Trying to add a new static content without specifying its name
        Given I want to add a new static content
        When I set its title to "Krzysztof Krawczyk"
        And I set its body to "Biography of the great Polish singer"
        And I add it
        Then I should be notified that name is required
        And the static content "Krzysztof Krawczyk" should not be added

    @ui
    Scenario: Trying to add a new static content without specifying its title
        Given I want to add a new static content
        When I set its name to "krzysztof-krawczyk"
        And I set its body to "Biography of the great Polish singer"
        And I add it
        Then I should be notified that title is required
        And the static content "Krzysztof Krawczyk" should not be added
