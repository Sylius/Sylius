@applying_catalog_promotions
Feature: Seeing applied catalog promotions on taxon
    In order to be informed about applied catalog promotion on product
    As a Customer
    I want to see discounted product assigned to certain taxon

    Background:
        Given the store operates on a single channel in "United States"
        And the store has "Clothes" taxonomy
        And the store has a "T-Shirt" configurable product
        And this product main taxon should be "Clothes"
        And this product has "PHP T-Shirt" variant priced at "$20.00"
        And the store has a "Mug" configurable product
        And this product main taxon should be "Clothes"
        And this product has "PHP Mug" variant priced at "$5.00"
        And there is a catalog promotion "Clothes sale" that reduces price by "30%" and applies on "Clothes" taxonomy

    @todo
    Scenario: Seeing applied catalog promotion only on proper variants
        When I view "PHP T-Shirt" variant of the "T-Shirt" product
        Then I should see this variant is discounted from "$20.00" to "$10.00" with "Clothes sale" promotion
        And "PHP Mug" variant of the "Mug" product should not be discounted
