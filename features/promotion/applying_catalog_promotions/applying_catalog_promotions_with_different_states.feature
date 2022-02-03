@applying_catalog_promotions
Feature: Applying catalog promotions with different states
    In order to process proper catalog promotions
    As a Visitor
    I want to see promotions that can be applied

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a "T-Shirt" configurable product
        And this product has "PHP T-Shirt" variant priced at "$20.00"
        And there is a catalog promotion "Winter sale" that reduces price by "50%" and applies on "PHP T-Shirt" variant

    @api @ui
    Scenario: Seeing catalog promotions that were processed successfully
        When I view "PHP T-Shirt" variant of the "T-Shirt" product
        Then I should see this variant is discounted from "$20.00" to "$10.00" with "Winter sale" promotion
