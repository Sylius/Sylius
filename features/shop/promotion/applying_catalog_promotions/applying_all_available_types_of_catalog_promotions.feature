@applying_catalog_promotions
Feature: Applying all available types of catalog promotions
    In order to see products in best prices
    As a Customer
    I want to see discounted products in the catalog

    Background:
        Given the store operates on a channel named "Web-US"
        And the store classifies its products as "Clothes"
        And the store has a "T-Shirt" configurable product
        And this product belongs to "Clothes"
        And this product has "PHP T-Shirt" variant priced at "$100.00" in "Web-US" channel
        And there is a catalog promotion "PHP sale" that reduces price by "10%" and applies on "PHP T-Shirt" variant
        And there is a catalog promotion "T-Shirt sale" that reduces price by "10%" and applies on "T-Shirt" product
        And there is a catalog promotion "Clothes sale" that reduces price by "10%" and applies on "Clothes" taxon
        And there is a catalog promotion "Fixed PHP sale" that reduces price by fixed "$5.00" in the "Web-US" channel and applies on "PHP T-Shirt" variant
        And there is a catalog promotion "Fixed T-Shirt sale" that reduces price by fixed "$5.00" in the "Web-US" channel and applies on "T-Shirt" product
        And there is a catalog promotion "Fixed Clothes sale" that reduces price by fixed "$5.00" in the "Web-US" channel and applies on "Clothes" taxon

    @api @ui
    Scenario: Applying multiple catalog promotions
        When I view "PHP T-Shirt" variant of the "T-Shirt" product
        Then I should see this variant is discounted from "$100.00" to "$57.90" with 6 promotions
