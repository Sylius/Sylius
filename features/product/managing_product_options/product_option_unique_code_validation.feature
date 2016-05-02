@managing_product_options
Feature: Product option unique code validation
    In order to uniquely identify product options
    As an Administrator
    I want to be prevented from adding two product options with the same code

    Background:
        Given the store is available in "English (United States)"
        And the store has a product option "T-Shirt size" with a code "t_shirt_size"
        And I am logged in as an administrator

    @ui @javascript
    Scenario: Trying to add product option with a taken code
        Given I want to create a new product option
        When I name it "T-Shirt color" in "English (United States)"
        And I specify its code as "t_shirt_size"
        And I add the "S" option value identified by "OV1"
        And I add the "M" option value identified by "OV2"
        And I try to add it
        Then I should be notified that product option with this code already exists
        And there should still be only one product option with code "t_shirt_size"
