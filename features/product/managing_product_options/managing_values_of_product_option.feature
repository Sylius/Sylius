@managing_product_options
Feature: Managing option values of a product option
    In order to add or remove option values in existing product options
    As an Administrator
    I want to be able to edit a product option and its option values

    Background:
        Given the store is available in "English (United States)"
        And the store has a product option "T-Shirt size" with a code "t_shirt_size"
        And this product option has the "S" option value with code "OV1"
        And this product option has also the "M" option value with code "OV2"
        And I am logged in as an administrator

    @ui @javascript
    Scenario: Adding an option value to an existing product option
        Given I want to modify the "T-Shirt size" product option
        And I add the "L" option value identified by "OV3"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this product option should have the "L" option value

    @ui @javascript @todo
    Scenario: Removing an option value from an existing product option
        Given this product option has also the "L" option value with code "OV3"
        And I want to modify the "T-Shirt size" product option
        When I delete the "L" option value of this product option
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this product option should not have the "L" option value

    @ui @javascript @todo
    Scenario: Removing and adding a new option value to an existing product option
        Given I want to modify the "T-Shirt size" product option
        When I delete the "M" option value of this product option
        And I add the "L" option value identified by "OV3"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this product option should not have the "M" option value
        And this product option should have the "L" option value
