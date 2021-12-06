@shopping_cart
Feature: Maintaining a last picked up cart
    In order to manage proper cart
    As a Visitor
    I want to be able to always maintain last picked up cart

    Background:
        Given the store operates on a single channel in "United States"

    @api
    Scenario: Having access to a last picked up cart
        When I pick up my cart
        And I pick up my cart again
        And I check details of my cart
        Then I should have empty cart
