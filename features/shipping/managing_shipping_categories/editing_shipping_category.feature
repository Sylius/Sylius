@managing_shipping_categories
Feature: Editing shipping method
    In order to change shipping category details
    As an Administrator
    I want to be able to edit a shipping category

    Background:
        Given the store operates on a single channel in "United States"
        And the store has "Standard" shipping category
        And I am logged in as an administrator

    @ui @api
    Scenario: Seeing disabled code field when editing shipping category
        When I modify a shipping category "Standard"
        Then I should not be able to edit its code

    @ui @api
    Scenario: Renaming the shipping category
        When I want to modify a shipping category "Standard"
        And I rename it to "Normal"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this shipping category name should be "Normal"
