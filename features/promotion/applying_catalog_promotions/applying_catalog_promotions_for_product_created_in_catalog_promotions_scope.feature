@applying_catalog_promotions
Feature: Applying catalog promotions for product created in catalog promotions scope
    In order to have proper discounts on new products that is created in catalog promotion
    As a Store Owner
    I want to have catalog promotion applied on new products

    Background:
        Given the store operates on a single channel in "United States"
        And the store classifies its products as "Clothes" and "Dishes"
        And I am logged in as an administrator

    @api @ui
    Scenario: Applying catalog promotion for product created in taxon that is in catalog promotion scope
        Given there is a catalog promotion "Clothes sale" that reduces price by "30%" and applies on "Clothes" taxon
        And the store has a "T-Shirt" configurable product
        And this product belongs to "Clothes"
        When I create a new "PHP T-Shirt" variant priced at "$20.00" for "T-Shirt" product in the "United States" channel
        Then the visitor should see "$14.00" as the price of the "T-Shirt" product in the "United States" channel
