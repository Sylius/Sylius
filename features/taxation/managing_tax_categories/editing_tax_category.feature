@taxation
Feature: Editing tax category
    In order to change tax classification of my products
    As an Administrator
    I want to be able to edit tax category

    Background:
        Given the store has "Alcohol" tax category with code "alcohol"
        And I am logged in as administrator

    @todo
    Scenario: Trying to change tax category code
        Given I want to modify tax category "Alcohol"
        When I change its code to "beverages"
        And I save my changes
        Then I should be notified that code cannot be changed
        And tax category "Alcohol" should still have code "alcohol"

    @todo
    Scenario: Seeing disabled code field when editing tax category
        When I want to modify tax category "Alcohol"
        Then the code field should be disabled

    @todo
    Scenario: Renaming the tax category
        Given I want to modify tax category "Alcohol"
        When I rename it to "Food & Alcohol"
        And I save my changes
        Then I should be notified about success
        And this tax category name should be "Food & Alcohol"
