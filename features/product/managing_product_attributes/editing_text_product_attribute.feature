@managing_product_attributes
Feature: Editing a text product attribute
    In order to change text product attributes applied to products
    As an Administrator
    I want to be able to edit a text product attribute

    Background:
        Given the store is available in "English (United States)"
        And I am logged in as an administrator

    @ui
    Scenario: Editing product attribute name
        Given the store has a text product attribute "T-Shirt cotton brand"
        When I want to edit this product attribute
        And I change its name to "T-Shirt material" in "English (United States)"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And the text attribute "T-Shirt material" should appear in the store

    @ui
    Scenario: Seeing disabled code field while editing a product attribute
        Given the store has a text product attribute "T-Shirt cotton brand" with code "t_shirt_brand"
        When I want to edit this product attribute
        Then the code field should be disabled

    @ui
    Scenario: Seeing disabled type field while editing a product attribute
        Given the store has a text product attribute "T-Shirt cotton brand" with code "t_shirt_brand"
        When I want to edit this product attribute
        Then the type field should be disabled
