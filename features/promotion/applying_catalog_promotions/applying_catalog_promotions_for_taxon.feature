@applying_catalog_promotions
Feature: Applying catalog promotions for taxon
    In order to be attracted to products
    As a Visitor
    I want to see discounted products in the catalog

    Background:
        Given the store operates on a single channel in "United States"
        And the store classifies its products as "Clothes" and "Dishes"
        And the store has a "T-Shirt" configurable product
        And this product belongs to "Clothes"
        And this product has "PHP T-Shirt" variant priced at "$20.00"
        And the store has a "Pants" configurable product
        And this product belongs to "Clothes"
        And this product has "Aladdin Pants" variant priced at "$100.00"
        And the store has a "Mug" configurable product
        And this product belongs to "Dishes"
        And this product has "PHP Mug" variant priced at "$5.00"
        And there is a catalog promotion "Clothes sale" that reduces price by "30%" and applies on "Clothes" taxon

    @api @ui
    Scenario: Applying simple catalog promotions
        When I view product "T-Shirt"
        Then I should see the product price "$14.00"
        And I should see the product original price "$20.00"

    @api @ui
    Scenario: Applying multiple catalog promotions
        Given there is a catalog promotion "Summer sale" that reduces price by "10%" and applies on "Clothes" taxon
        When I view product "T-Shirt"
        Then I should see the product price "$12.60"
        And I should see the product original price "$20.00"

    @api @ui
    Scenario: Not applying catalog promotion if it's not eligible
        When I view product "Mug"
        Then I should see the product price "$5.00"
        And I should see this product has no catalog promotion applied
