@applying_catalog_promotions
Feature: Applying catalog promotions with fixed discount action
    In order to be attracted to specific products
    As a Visitor
    I want to see discounted products in the catalog

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a "T-Shirt" configurable product
        And this product has "PHP T-Shirt" variant priced at "$20.00"
        And this product has "Java T-Shirt" variant priced at "$30.00"
        And there is a catalog promotion "Winter sale" that reduces price by fixed "$10.00" in the "United States" channel and applies on "T-Shirt" product

    @api @ui
    Scenario: Applying simple catalog promotion
        When I view "PHP T-Shirt" variant of the "T-Shirt" product
        Then I should see this variant is discounted from "$20.00" to "$10.00" with "Winter sale" promotion

    @api @ui @mink:chromedriver
    Scenario: Applying simple catalog promotion on another variant
        When I view "Java T-Shirt" variant of the "T-Shirt" product
        Then I should see this variant is discounted from "$30.00" to "$20.00" with "Winter sale" promotion

    @api @ui
    Scenario: Applying multiple catalog promotions
        Given there is a catalog promotion "Christmas sale" that reduces price by fixed "$5.00" in the "United States" channel and applies on "PHP T-Shirt" variant
        When I view "PHP T-Shirt" variant of the "T-Shirt" product
        Then I should see this variant is discounted from "$20.00" to "$5.00" with "Winter sale" and "Christmas sale" promotions
