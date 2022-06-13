@applying_catalog_promotions
Feature: Seeing applied catalog promotions on products list
    In order to be attracted to products
    As a Customer
    I want to see discounted products in the catalog

    Background:
        Given the store operates on a single channel in "United States"
        And the store classifies its products as "T-Shirts"
        And the store has a "Programming T-Shirt" configurable product
        And this product has "PHP T-Shirt" variant priced at "$20.00"
        And this product has "Kotlin T-Shirt" variant priced at "$40.00"
        And this product belongs to "T-Shirts"
        And the store has a "Nerd T-Shirt" configurable product
        And this product has "The Witcher T-Shirt" variant priced at "$30.00"
        And this product has "LotR T-Shirt" variant priced at "$50.00"
        And this product belongs to "T-Shirts"
        And there is a catalog promotion "Winter sale" that reduces price by "50%" and applies on "PHP T-Shirt" variant

    @ui @no-api
    Scenario: Seeing applied catalog promotion on products list
        When I browse products from taxon "T-Shirts"
        Then I should see "Programming T-Shirt" product discounted from "$20.00" to "$10.00" by "Winter sale" on the list
        And I should see "Nerd T-Shirt" product not discounted on the list
