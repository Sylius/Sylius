@applying_catalog_promotions
Feature: Applying catalog promotion with different scopes defined
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
        And there is a catalog promotion "Clothes sale" that reduces price by "30%" and applies on "Clothes" taxon
        And it applies also on "PHP T-Shirt" variant

    @api @ui
    Scenario: Applying catalog promotion only once on variant defined in two scopes
        When I view product "T-Shirt"
        Then I should see the product price "$14.00"
        And I should see the product original price "$20.00"

    @api @ui
    Scenario: Applying catalog promotion on variant
        When I view product "Pants"
        Then I should see the product price "$70.00"
        And I should see the product original price "$100.00"
