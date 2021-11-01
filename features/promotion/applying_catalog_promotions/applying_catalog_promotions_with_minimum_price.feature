@applying_catalog_promotions
Feature: Applying catalog promotions with minimum price
    In order to avoid too many promotions applied on product
    As a Visitor
    I want to see discounted products in the catalog up to minimum price

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a "T-Shirt" configurable product
        And this product has "PHP T-Shirt" variant priced at "$20.00"
        And "PHP T-Shirt" variant has minimum price "$15.00" in "United States" channel
        And there is a catalog promotion "Winter sale" that reduces price by "80%" and applies on "PHP T-Shirt" variant

    @api @ui
    Scenario: Applying promotion up to minimum price
        When I view "PHP T-Shirt" variant of the "T-Shirt" product
        Then I should see this variant is discounted from "$20.00" to "$15.00" with "Winter sale" promotion

    @api @ui
    Scenario: Not applying promotion if product is priced on same price as its minimum price
        Given there is a catalog promotion "T-shirt Sale" that reduces price by "40%" and applies on "PHP T-Shirt" variant
        When I view "PHP T-Shirt" variant of the "T-Shirt" product
        Then I should see this variant is discounted from "$20.00" to "$15.00" with only "Winter sale" promotion
