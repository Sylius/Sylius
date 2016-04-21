@managing_product_options
Feature: Adding a new product option
    In order to have different product option
    As an Administrator
    I want to be able to add a new product option to the registry

    Background:
        Given the store operates on a single channel in "France"
        And I am logged in as an administrator

    @ui @javascript
    Scenario: Adding a new product option with two required option values
        Given I want to create a new product option
        When I name it "T-Shirt size" in "English (United States)"
        And I specify its code as "t_shirt_size"
        And I add the option value with code "OV1" and value "S"
        And I add the option value with code "OV2" and value "M"
        And I add it
        Then I should be notified that it has been successfully created
        And the product option "T-Shirt size" should appear in the registry
