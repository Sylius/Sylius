@managing_product_options
Feature: Managing option values of a product option
    In order to add or remove option values in existing product options
    As an Administrator
    I want to be able to edit a product option and its option values

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product option "T-Shirt size" with a code "t_shirt_size"
        And this product option has the "S" option value with code "OV1"
        And this product option has also the "M" option value with code "OV2"
        And I am logged in as an administrator

    @api @ui @javascript
    Scenario: Adding an option value to an existing product option
        When I want to modify the "T-Shirt size" product option
        And I add the "L" option value identified by "OV3"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this product option should have the "L" option value

    @api @ui @mink:chromedriver
    Scenario: Removing an option value from an existing product option
        Given this product option has also the "L" option value with code "OV3"
        When I want to modify the "T-Shirt size" product option
        And I delete the "OV3" option value of this product option
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this product option should not have the "L" option value

    @api @ui @mink:chromedriver
    Scenario: Removing and adding a new option value to an existing product option
        When I want to modify the "T-Shirt size" product option
        And I delete the "OV2" option value of this product option
        And I add the "L" option value identified by "OV3"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this product option should not have the "M" option value
        And this product option should have the "L" option value

    @api @ui @mink:chromedriver
    Scenario: Removing product option value that is in use by product variant
        Given the store has a "Car" configurable product
        And this product has option "Model" with values "Sedan", "Kombi" and "Cabrio"
        And this product has "Car-Variant-1" variant priced at "$20.00" configured with "Sedan" option value
        And this product has "Car-Variant-2" variant priced at "$25.00" configured with "Kombi" option value
        And this product has "Car-Variant-3" variant priced at "$50.00" configured with "Cabrio" option value
        When I want to modify the "Model" product option
        And I delete the "Sedan" option value of this product option
        And I try to save my changes
        Then I should be notified that it is in use
        And product option "Model" should still have the "Sedan" option value
