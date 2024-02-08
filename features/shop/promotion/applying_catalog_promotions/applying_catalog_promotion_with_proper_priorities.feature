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
        And there is a catalog promotion "Autumn sale" with priority 10 that reduces price by "25%" and applies on "PHP T-Shirt" variant
        And there is a catalog promotion "Winter sale" with priority 40 that reduces price by fixed "$10.00" in the "United States" channel and applies on "T-Shirt" product
        And there is a catalog promotion "Spring sale" with priority 20 that reduces price by fixed "$5.00" in the "United States" channel and applies on "Clothes" taxon
        And there is a catalog promotion "Summer sale" with priority 30 that reduces price by "50%" and applies on "PHP T-Shirt" variant

    @api @ui
    Scenario: Applying catalog promotion with descending order by their priority
        When I view "PHP T-Shirt" variant of the "T-Shirt" product
        Then I should see this variant is discounted from "$100.00" to "$30.00" with "Winter sale", "Summer sale", "Spring sale" and "Autumn sale" promotions
