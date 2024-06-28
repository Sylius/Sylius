@managing_product_options
Feature: Filtering product options
    In order quickly find specific product options
    As an Administrator
    I want to filter product options by name and code

    Background:
        Given the store has a product option "T-Shirt size" with a code "t_shirt_size_abc"
        And the store has a product option "T-Shirt color" with a code "t_shirt_color_xyz"
        And the store has a product option "Jeans size" with a code "jeans_size_xyz"
        And I am logged in as an administrator
        And I am browsing product options

    @ui @todo-api
    Scenario: Filtering product options by name
        When I search for product options with "T-Shirt"
        Then I should see 2 product options in the list
        And the product option "T-Shirt size" should be in the registry
        And the product option "T-Shirt color" should be in the registry

    @ui @todo-api
    Scenario: Filtering product options by code
        When I search for product options with "xyz"
        Then I should see 2 product options in the list
        And the product option "T-Shirt color" should be in the registry
        And the product option "Jeans size" should be in the registry
