@managing_products
Feature: Filtering products by code and name
    In order to quickly find products
    As an Administrator
    I want to search for a specific product

    Background:
        Given the store has a product "MacBook Air" with code "air-ai"
        And the store has a product "MacBook Pro" with code "pro-pr"
        And the store has a product "HP Pro" with code "hp-pr"
        And I am logged in as an administrator
        And I am browsing products

    @ui @api-todo
    Scenario: Searching for a product by code
        When I search for products with "-pr"
        Then I should see 2 products in the list
        And I should see a product with name "MacBook Pro"
        And I should see a product with name "HP Pro"

    @ui @api-todo
    Scenario: Searching for a product by name
        When I search for products with "Mac"
        Then I should see 2 products in the list
        And I should see a product with name "MacBook Air"
        And I should see a product with name "MacBook Pro"
