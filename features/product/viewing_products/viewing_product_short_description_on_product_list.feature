@viewing_products
Feature: Viewing a product short description on product list
    In order to see short description on product list
    As a Visitor
    I want to be able to view short description of product on product list

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "T-Shirt watermelon"
        And the short description of product "T-Shirt watermelon" is "Great T-Shirt"

    @api
    Scenario: Viewing a short description on product list
        When I browse products
        Then I should see the product "T-Shirt watermelon" with short description "Great T-Shirt"

