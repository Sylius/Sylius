@managing_product_options
Feature: Browsing product options
    In order to see all product options in the store
    As an Administrator
    I want to be able to browse product options

    @ui
    Scenario: Browsing defined product options
        Given I am logged in as an administrator
        And the store has a product option "T-Shirt size" with a code "t_shirt_size"
        And the store has a product option "T-Shirt color" with a code "t_shirt_color"
        When I browse product options
        Then I should see 2 product options in the list
        And the product option "T-Shirt size" should be in the registry
        And the product option "T-Shirt color" should be in the registry
