@managing_product_attributes
Feature: Editing a text product attribute
    In order to change text product attributes applied to products
    As an Administrator
    I want to be able to edit a text product attribute

    Background:
        Given the store is available in "English (United States)"
        And the store has a text product attribute "T-Shirt cotton brand" with code "t_shirt_brand"
        And I am logged in as an administrator

    @ui @api
    Scenario: Editing product attribute name
        When I want to edit this product attribute
        And I change its name to "T-Shirt material" in "English (United States)"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And the text attribute "T-Shirt material" should appear in the store

    @ui @api
    Scenario: Being unable to change code of an existing product attribute
        When I want to edit this product attribute
        Then I should not be able to edit its code

    @ui @api
    Scenario: Being unable to change type of an existing product attribute
        When I want to edit this product attribute
        Then I should not be able to edit its type
