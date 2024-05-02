@managing_product_attributes
Feature: Editing a select product attribute
    In order to change select product attributes applied to products
    As an Administrator
    I want to be able to edit a select product attribute

    Background:
        Given the store is available in "English (United States)"
        And the store has a select product attribute "T-Shirt material" with value "Banana skin"
        And I am logged in as an administrator

    @ui @api
    Scenario: Editing a select product attribute name
        When I want to edit this product attribute
        And I change its name to "T-Shirt material" in "English (United States)"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And the select attribute "T-Shirt material" should appear in the store

    @ui @api
    Scenario: Editing a select product attribute value
        When I want to edit this product attribute
        And I change its value "Banana skin" to "Orange skin"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this product attribute should have value "Orange skin"

    @ui @javascript @api
    Scenario: Adding a new value to an existing select product attribute
        When I want to edit this product attribute
        And I add value "Orange skin" in "English (United States)"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this product attribute should have value "Orange skin"

    @ui @javascript @api
    Scenario: Deleting a value from an existing select product attribute
        When I want to edit this product attribute
        And I delete value "Banana skin"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this product attribute should not have value "Banana skin"

    @ui @api
    Scenario: Being unable to change code of an existing product attribute
        When I want to edit this product attribute
        Then I should not be able to edit its code

    @ui @api
    Scenario: Being unable to change type of an existing product attribute
        When I want to edit this product attribute
        Then I should not be able to edit its type
