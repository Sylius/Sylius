@applying_catalog_promotions
Feature: Applying catalog promotions with taking minimum price into account
    In order to avoid too much discount applied on product
    As a Visitor
    I want to see discounted products in the catalog up to minimum price

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a "T-Shirt" configurable product
        And this product has "PHP T-Shirt" variant priced at "$20.00"
        And the "PHP T-Shirt" variant has minimum price of "$15.00" in the "United States" channel

    @api @ui
    Scenario: Applying percentage discount up to minimum price
        Given there is a catalog promotion "Winter sale" that reduces price by "80%" and applies on "PHP T-Shirt" variant
        When I view "PHP T-Shirt" variant of the "T-Shirt" product
        Then I should see this variant is discounted from "$20.00" to "$15.00" with "Winter sale" promotion

    @api @ui
    Scenario: Applying fixed discount up to minimum price
        Given there is a catalog promotion "Winter sale" that reduces price by fixed "$10.00" in the "United States" channel and applies on "PHP T-Shirt" variant
        When I view "PHP T-Shirt" variant of the "T-Shirt" product
        Then I should see this variant is discounted from "$20.00" to "$15.00" with "Winter sale" promotion

    @api @ui
    Scenario: Not applying promotion if product is priced on same price as its minimum price
        Given there is a catalog promotion "Winter sale" that reduces price by "80%" and applies on "PHP T-Shirt" variant
        And there is a catalog promotion "T-Shirt Sale" that reduces price by "40%" and applies on "PHP T-Shirt" variant
        When I view "PHP T-Shirt" variant of the "T-Shirt" product
        Then I should see this variant is discounted from "$20.00" to "$15.00" with only "Winter sale" promotion
