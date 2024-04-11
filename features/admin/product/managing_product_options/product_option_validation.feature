@managing_product_options
Feature: Product option validation
    In order to avoid making mistakes when managing a product option
    As an Administrator
    I want to be prevented from adding it without specifying required fields

    Background:
        Given the store is available in "English (United States)"
        And the store has a product option "T-Shirt color" with a code "t_shirt_color"
        And I am logged in as an administrator

    @ui @api
    Scenario: Trying to add a new product option without specifying its code
        When I want to create a new product option
        And I name it "T-Shirt size" in "English (United States)"
        But I do not specify its code
        And I try to add it
        Then I should be notified that code is required
        And the product option with name "T-Shirt size" should not be added

    @no-ui @api
    Scenario: Trying to add a new product option translation in unexsting locale
        When I want to modify the "T-Shirt color" product option
        And I name it "T-Shirt color" in "French (France)"
        And I try to save my changes
        Then I should be notified that the locale is not available

    @ui @api
    Scenario: Trying to add a new product option with a too long code
        When I want to create a new product option
        And I name it "T-Shirt size" in "English (United States)"
        And I specify a too long code
        And I try to add it
        Then I should be notified that code is too long

    @ui @api
    Scenario: Trying to add a new product option without specifying its name
        When I want to create a new product option
        And I specify its code as "t_shirt_size"
        But I do not name it
        And I try to add it
        Then I should be notified that name is required
        And the product option with code "t_shirt_size" should not be added

    @ui @api
    Scenario: Trying to remove name from an existing product option
        When I want to modify the "T-Shirt color" product option
        And I remove its name from "English (United States)" translation
        And I try to save my changes
        Then I should be notified that name is required
        And this product option should still be named "T-Shirt color"
