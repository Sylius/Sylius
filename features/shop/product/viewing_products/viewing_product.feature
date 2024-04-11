@viewing_products
Feature: Viewing a product details
    In order to see products detailed information
    As a Visitor
    I want to be able to view a single product

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "T-Shirt banana"
        And the description of product "T-Shirt banana" is "You must have this beautiful T-Shirt"

    @ui @api
    Scenario: Viewing a detailed page with product's name
        When I check this product's details
        Then I should see the product name "T-Shirt banana"
        And I should see the product description "You must have this beautiful T-Shirt"
