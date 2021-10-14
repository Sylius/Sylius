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
        And this product has "PHP T-Shirt" variant originally priced at "$20.00" in "Web-US" channel
        And the store has a "Mug" configurable product
        And this product belongs to "Dishes"
        And this product has "Coffee Mug" variant originally priced at "$5.00" in "Web-US" channel
        And there is a catalog promotion "Clothes sale" that reduces price by "30%" and applies on "Clothes" taxon

    @api
    Scenario: Applying multiple catalog promotions
        Given there is a catalog promotion "Winter sale" available in "Web-US" channel that reduces price by "30%" and applies on "PHP T-shirt" variant
        When I view "PHP T-Shirt" variant of the "T-Shirt" product
        Then I should see this variant is discounted from "$20.00" to "$9.80" with "Clothes sale" promotion
