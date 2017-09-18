@managing_customer_tax_categories
Feature: Editing a customer tax category
    In order to change tax classification of customers
    As an Administrator
    I want to be able to edit a customer tax category

    Background:
        Given the store has a customer tax category "Retail" with a code "retail"
        And I am logged in as an administrator

    @ui
    Scenario: Renaming a customer tax category
        When I want to modify a customer tax category "Retail"
        And I rename it to "Wholesale"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this customer tax category name should be "Wholesale"

    @ui
    Scenario: Changing a description of a customer tax category
        Given this customer tax category has a description specified as "Retail customers."
        When I want to modify a customer tax category "Retail"
        And I change description to "General tax category of customers."
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this customer tax category description should be "General tax category of customers."

    @ui
    Scenario: Seeing disabled code field when editing a customer tax category
        When I want to modify a customer tax category "Retail"
        Then the code field should be disabled
