@viewing_products
Feature: Viewing product with all variants disabled
    In order to see product details
    As a Visitor
    I want to be able to view product even if all its variants are not available

    Background:
        Given the store operates on a single channel
        And the store classifies its products as "T-Shirts"
        And the store has a "Super Cool T-Shirt" configurable product
        And this product has "Small", "Medium" and "Large" variants
        And all variants of this product are disabled
        And this product belongs to "T-Shirts"

    @ui @api
    Scenario: Viewing product with all variants disabled
        When I check this product's details
        Then I should see the product name "Super Cool T-Shirt"

    @ui @api
    Scenario: Viewing product with all variants disabled from taxon page
        When I browse products from taxon "T-Shirts"
        Then I should see the product "Super Cool T-Shirt"
