@managing_product_options
Feature: Product option validation
    In order to avoid making mistakes when managing a product option
    As an Administrator
    I want to be prevented from adding it without specifying required fields

    Background:
        Given the store is available in "English (United States)"
        And the store has a product option "T-Shirt color" with a code "t_shirt_color"
        And I am logged in as an administrator

    @ui @javascript
    Scenario: Trying to add a new product option without specifying its code
        Given I want to create a new product option
        When I name it "T-Shirt size" in "English (United States)"
        But I do not specify its code
        And I add the "S" option value identified by "OV1"
        And I add the "M" option value identified by "OV2"
        And I try to add it
        Then I should be notified that code is required
        And the product option with name "T-Shirt size" should not be added

    @ui @javascript
    Scenario: Trying to add a new product option without specifying its name
        Given I want to create a new product option
        When I specify its code as "t_shirt_size"
        But I do not name it
        And I add the "S" option value identified by "OV1"
        And I add the "M" option value identified by "OV2"
        And I try to add it
        Then I should be notified that name is required
        And the product option with code "t_shirt_size" should not be added

    @ui
    Scenario: Trying to remove name from an existing product option
        Given I want to modify the "T-Shirt color" product option
        When I remove its name from "English (United States)" translation
        And I try to save my changes
        Then I should be notified that name is required
        And this product option should still be named "T-Shirt color"

    @ui
    Scenario: Trying to add a new product option without any option values
        Given I want to create a new product option
        When I name it "T-Shirt size" in "English (United States)"
        And I specify its code as "t_shirt_size"
        But I do not add an option value
        And I try to add it
        Then I should be notified that at least two option values are required
        And the product option with name "T-Shirt size" should not be added

    @ui @javascript
    Scenario: Trying to add a new product option with one option value
        Given I want to create a new product option
        When I name it "T-Shirt size" in "English (United States)"
        And I specify its code as "t_shirt_size"
        And I add the "S" option value identified by "OV1"
        And I try to add it
        Then I should be notified that at least two option values are required
        And the product option with name "T-Shirt size" should not be added
