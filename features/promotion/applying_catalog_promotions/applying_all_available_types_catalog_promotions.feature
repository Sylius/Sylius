@applying_catalog_promotions
Feature: Applying all available catalog promotions
    In order to see products in best prices
    As a Customer
    I want to see discounted products in the catalog

    Background:
        Given the store operates on a channel named "Web-US"
        And the store classifies its products as "Clothes" and "Dishes"
        And the store has a "T-Shirt" configurable product
        And this product belongs to "Clothes"
        And this product has "PHP T-Shirt" variant priced at "$20.00" in "Web-US" channel
        And the store has a "Mug" configurable product
        And this product belongs to "Dishes"
        And this product has "Coffee Mug" variant priced at "$5.00" in "Web-US" channel
        And there is a catalog promotion "Clothes sale" that reduces price by "30%" and applies on "Clothes" taxon
        And there is a catalog promotion "T-Shirt sale" that reduces price by "30%" and applies on "T-Shirt" product
        And there is a catalog promotion "Winter sale" that reduces price by "30%" and applies on "PHP T-Shirt" variant

    @api @ui
    Scenario: Applying multiple catalog promotions
        When I view "PHP T-Shirt" variant of the "T-Shirt" product
        Then I should see this variant is discounted from "$20.00" to "$6.86" with "Clothes sale", "T-Shirt sale" and "Winter sale" promotions
