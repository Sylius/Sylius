@static_content
Feature: Showing static content
    In order to access information about the shop
    As a Customer
    I want to be able to browse through its pages

    Background:
        Given the store operates on a single channel in "France"

    @ui
    Scenario: Showing static content
        Given the store has static content "Krzysztof Krawczyk" with name "krzysztof-krawczyk"
        When I access static content with name "krzysztof-krawczyk"
        Then I should see that static content
