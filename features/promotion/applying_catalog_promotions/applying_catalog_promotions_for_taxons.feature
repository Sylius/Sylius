@applying_catalog_promotions
Feature: Applying catalog promotions for taxons
    In order to be attracted to products
    As a Customer
    I want to see discounted products in the catalog

    Background:
        Given the store operates on a single channel in "United States"
        And the store classifies its products as "T-Shirts" and "Mugs"
        And the store has a product "PHP T-Shirt" priced at "$100.00"
        And it belongs to "T-Shirts"
        And the store has a product "PHP Mug" priced at "$10.00"
        And it belongs to "Mugs"
        And there is a catalog promotion with "winter_sale" code and "Winter sale" name
        And it will be applied on "T-Shirts" taxon
        And it will reduce price by 50%

    @api
    Scenario: Applying simple catalog promotions
        When I view product "PHP T-Shirt"
        Then I should see the product price "$50.00"
        And I should see the product original price "$100.00"

    @api
    Scenario: Not applying catalog promotion if it's not eligible
        When I view product "PHP Mug"
        Then I should see the product price "$10.00"
        And I should see the product original price "$10.00"
