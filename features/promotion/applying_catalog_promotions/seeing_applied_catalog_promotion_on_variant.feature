@applying_catalog_promotions
Feature: Seeing applied catalog promotions on variant
    In order to be informed about applied catalog promotion on product
    As a Customer
    I want to see discounted product variant

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a "T-Shirt" configurable product
        And this product has "PHP T-Shirt" variant priced at "$20.00"
        And this product has "Sylius T-Shirt" variant priced at "$15.00"
        And there is a catalog promotion "Winter sale" that reduces price by "50%" and applies on "PHP T-Shirt" variant

    @api @ui
    Scenario: Seeing applied catalog promotion on variant
        When I view "PHP T-Shirt" variant of the "T-Shirt" product
        Then I should see this variant is discounted from "$20.00" to "$10.00" with "Winter sale" promotion

    @api @ui @javascript
    Scenario: Seeing no applied catalog promotion on variant
        When I view "Sylius T-Shirt" variant of the "T-Shirt" product
        Then I should see this variant is not discounted
