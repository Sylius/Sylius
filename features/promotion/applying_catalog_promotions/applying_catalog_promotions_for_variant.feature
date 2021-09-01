@applying_catalog_promotions
Feature: Applying catalog promotions for variants
    In order to be attracted to products
    As a Customer
    I want to see discounted products in the catalog

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a "T-Shirt" configurable product
        And this product has "PHP T-Shirt" variant priced at "$20.00"
        And this product has "Kotlin T-Shirt" variant priced at "$40.00"
        And this product has "Python T-Shirt" variant priced at "$40.00"
        And the store has a "Mug" configurable product
        And this product has "PHP Mug" variant priced at "$5.00"
        And this product has "Kotlin Mug" variant priced at "$7.00"
        And there is a catalog promotion with "winter_sale" code and "Winter sale" name
        And it will be applied on "PHP T-Shirt" variant
        And it will reduce price by "30%"

    @api
    Scenario: Applying simple catalog promotions
        When I view product "T-Shirt"
        Then I should see the product price "$14.00"
        And I should see the product original price "$20.00"

    @api
    Scenario: Not applying catalog promotion if it's not eligible
        When I view product "Mug"
        Then I should see the product price "$5.00"
        And I should see the product original price "$5.00"
