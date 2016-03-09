@legacy @metadata
Feature: Metadata rendering & processing
    In order to render metadata dynamically
    As a store owner
    I want to be able to use expressions and Twig templating engine in metadata properties

    Background:
        Given store has default configuration
        And there are products:
            | name   | price |
            | Banana | 4.20  |
        And product "Banana" has the following page metadata:
            | Title | {{ subject.name }} - Sylius |
        And all products are assigned to the default channel

    Scenario: Rendering processed page metadata
        When I am on the product page for "Banana"
        Then I should see "Banana - Sylius" as page title
