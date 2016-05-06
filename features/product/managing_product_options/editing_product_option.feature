@managing_product_options
Feature: Editing product options
    In order to change which product option details
    As an Administrator
    I want to be able to edit a product option

    Background:
        Given the store is available in "English (United States)"
        And the store has a product option "T-Shirt size" with a code "t_shirt_size"
        And I am logged in as an administrator

    @ui
    Scenario: Renaming the product option
        Given this product option has the "S" option value with code "t_shirt_size_s"
        And this product option has also the "M" option value with code "t_shirt_size_m"
        And I want to modify the "T-Shirt size" product option
        When I rename it to "T-Shirt color" in "English (United States)"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this product option name should be "T-Shirt color"

    @ui
    Scenario: Seeing disabled code field while editing product option
        Given I want to modify the "T-Shirt size" product option
        Then the code field should be disabled
