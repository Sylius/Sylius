@managing_product_attributes
Feature: Editing a select product attribute
    In order to change select product attributes applied to products
    As an Administrator
    I want to be able to edit a select product attribute

    Background:
        Given the store is available in "English (United States)"
        And the store has a select product attribute "T-shirt material" with value "Banana skin"
        And I am logged in as an administrator

    @ui
    Scenario: Editing a select product attribute name
        When I want to edit this product attribute
        And I change its name to "T-shirt material" in "English (United States)"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And the select attribute "T-shirt material" should appear in the store

    @ui
    Scenario: Editing a select product attribute value
        When I want to edit this product attribute
        And I change its value "Banana skin" to "Orange skin"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this product attribute should have value "Orange skin"

    @ui @javascript
    Scenario: Adding a new value to an existing select product attribute
        When I want to edit this product attribute
        And I add value "Orange skin" in "English (United States)"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this product attribute should have value "Orange skin"

    @ui @javascript
    Scenario: Deleting a value from an existing select product attribute
        When I want to edit this product attribute
        And I delete value "Banana skin"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this product attribute should not have value "Banana skin"

    @ui
    Scenario: Seeing disabled code field while editing a product attribute
        When I want to edit this product attribute
        Then the code field should be disabled

    @ui
    Scenario: Seeing disabled type field while editing a product attribute
        When I want to edit this product attribute
        Then the type field should be disabled
