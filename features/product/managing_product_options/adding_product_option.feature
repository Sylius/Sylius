@managing_product_options
Feature: Adding a new product option
    In order to sell various options of the same product
    As an Administrator
    I want to be able to add a new product option to the registry

    Background:
        Given the store is available in "English (United States)"
        And I am logged in as an administrator

    @ui @mink:chromedriver @api
    Scenario: Adding a new product option with two required option values
        When I want to create a new product option
        And I name it "T-Shirt size" in "English (United States)"
        And I specify its code as "t_shirt_size"
        And I add the "S" option value identified by "OV1"
        And I add the "M" option value identified by "OV2"
        And I add it
        Then I should be notified that it has been successfully created
        And the product option "T-Shirt size" should appear in the registry
        And product option "T-Shirt size" should have the "S" option value
        And product option "T-Shirt size" should have the "M" option value

    @ui @api
    Scenario: Adding a new product option without any option values
        When I want to create a new product option
        And I name it "T-Shirt size" in "English (United States)"
        And I specify its code as "t_shirt_size"
        But I do not add an option value
        And I try to add it
        Then I should be notified that it has been successfully created
        And the product option "T-Shirt size" should appear in the registry

    @ui @javascript @api
    Scenario: Adding a new product option with one option value
        When I want to create a new product option
        And I name it "T-Shirt size" in "English (United States)"
        And I specify its code as "t_shirt_size"
        And I add the "S" option value identified by "OV1"
        And I try to add it
        Then I should be notified that it has been successfully created
        And the product option "T-Shirt size" should appear in the registry
        And product option "T-Shirt size" should have the "S" option value
