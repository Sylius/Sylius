@applying_catalog_promotions
Feature: Reapplying catalog promotions on variants once the product’s taxon changes
    In order to have proper discounts in product catalog
    As a Store Owner
    I want to have discounts reapplied on variants once the product’s taxon changes

    Background:
        Given the store operates on a channel named "Web-US" with hostname "web-us"
        And the store classifies its products as "Clothes" and "Dishes"
        And the store has a "T-Shirt" configurable product
        And this product belongs to "Clothes"
        And this product has "PHP T-Shirt" variant priced at "$100.00"
        And the store has a "Mug" configurable product
        And this product has "PHP Mug" variant priced at "$10.00"
        And there is a catalog promotion "Winter sale" that reduces price by "30%" and applies on "Clothes" taxon
        And there is another catalog promotion "Summer sale" that reduces price by "50%" and applies on "Dishes" taxon
        And I am logged in as an administrator

    @api @ui
    Scenario: Removing a taxon from a product
        When I change that the "T-Shirt" product does not belong to the "Clothes" taxon
        Then the visitor should see that the "PHP T-Shirt" variant is not discounted

    @api @ui
    Scenario: Adding a taxon to a product
        When I assign the "Dishes" taxon to the "Mug" product
        Then the visitor should see that the "PHP Mug" variant is discounted from "$10.00" to "$5.00" with "Summer sale" promotion
