@applying_catalog_promotions
Feature: Applying catalog promotion with proper priorities
    In order to be attracted to products
    As a Visitor
    I want to see discounted products in the catalog

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a "T-Shirt" configurable product
        And this product has "PHP T-Shirt" variant priced at "$100.00"
        And there is a catalog promotion "Clothes sale" with priority 10 that reduces price by "30%" and applies on "PHP T-Shirt" variant
        And there is a catalog promotion "Winter sale" with priority 100 that reduces price by "50%" and applies on "PHP T-Shirt" variant

    @api @ui
    Scenario: Applying catalog promotion with descending order by their priority
        When I view "PHP T-Shirt" variant of the "T-Shirt" product
        Then I should see this variant is discounted from "$100.00" to "$35.00" with "Winter sale" and "Clothes sale" promotions
