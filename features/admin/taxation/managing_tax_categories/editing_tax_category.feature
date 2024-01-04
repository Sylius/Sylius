@managing_tax_categories
Feature: Editing tax category
    In order to change tax classification of my products
    As an Administrator
    I want to be able to edit tax category

    Background:
        Given the store has a tax category "Alcohol" with a code "alcohol"
        And I am logged in as an administrator

    @todo
    Scenario: Trying to change tax category code
        When I want to modify a tax category "Alcohol"
        And I change its code to "beverages"
        And I save my changes
        Then I should be notified that code cannot be changed
        And tax category "Alcohol" should still have code "alcohol"

    @todo @ui @api
    Scenario: Seeing disabled code field when editing tax category
        When I want to modify a tax category "Alcohol"
        Then I should not be able to edit its code

    @todo @ui @api
    Scenario: Renaming the tax category
        When I want to modify a tax category "Alcohol"
        And I rename it to "Food & Alcohol"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this tax category name should be "Food & Alcohol"
