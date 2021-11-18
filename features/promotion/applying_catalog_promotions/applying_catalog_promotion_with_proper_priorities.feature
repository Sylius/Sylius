@applying_catalog_promotions
Feature: Applying catalog promotion with proper priorities
    In order to be attracted to products
    As a Visitor
    I want to see discounted products in the catalog

    Background:
        Given the store operates on a single channel in "United States"
        And the store classifies its products as "Clothes"
        And the store has a "T-Shirt" configurable product
        And this product belongs to "Clothes"
        And this product has "PHP T-Shirt" variant priced at "$100.00"
        And there is a catalog promotion "Variant 10 sale" with priority 10 that reduces price by "30%" and applies on "PHP T-Shirt" variant
        And there is a catalog promotion "Variant 100 sale" with priority 100 that reduces price by "50%" and applies on "PHP T-Shirt" variant
        And there is a catalog promotion "Product 200 sale" with priority 200 that reduces price by "10%" and applies on "T-Shirt" product
        And there is a catalog promotion "Product 20 sale" with priority 20 that reduces price by "10%" and applies on "T-Shirt" product
        And there is a catalog promotion "Taxon 150 sale" with priority 150 that reduces price by "10%" and applies on "Clothes" taxon
        And there is a catalog promotion "Taxon 15 sale" with priority 15 that reduces price by "10%" and applies on "Clothes" taxon

    @ui
    Scenario: Applying catalog promotion with descending order by their priority
        When I view "PHP T-Shirt" variant of the "T-Shirt" product
        Then I should see this variant is discounted from "$100.00" to "$22.96" with "Product 200 sale", "Taxon 150 sale", "Variant 100 sale", "Product 20 sale", "Taxon 15 sale" and "Variant 10 sale" promotions
