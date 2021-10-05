@applying_catalog_promotions
Feature: Reapplying catalog promotions on taxon once its data changes
    In order to have proper discounts on taxon
    As a Store Owner
    I want to have discounts reapplied on taxon once its data changes

    Background:
        Given the store operates on a channel named "Web-US" with hostname "web-us"
        And the store classifies its products as "Clothes" and "Dishes"
        And the store has a "T-Shirt" configurable product
        And this product belongs to "Clothes"
        And this product has "PHP T-Shirt" variant priced at "$100.00"
        And the store has a "Mug" configurable product
        And this product belongs to "Dishes"
        And this product has "PHP Mug" variant priced at "$10.00"
        And I am logged in as an administrator

    @api
    Scenario: Reapplying catalog promotion after changing the taxon of catalog promotion
        Given there is a catalog promotion "Winter sale" that reduces price by "30%" and applies on "Clothes" taxon
        When I edit "Winter sale" catalog promotion to be applied on "Dishes" taxon
        Then the visitor should see "$100.00" as the price of the "T-Shirt" product in the "Web-US" channel
        And the visitor should see "$7.00" as the price of the "Mug" product in the "Web-US" channel
        And the visitor should still see "$10.00" as the original price of the "Mug" product in the "Web-US" channel

    @api
    Scenario: Reapplying catalog promotion after changing scope from variant to taxon based
        Given there is a catalog promotion "Winter sale" that reduces price by "30%" and applies on "PHP T-Shirt" variant
        When I edit "Winter sale" catalog promotion to be applied on "Dishes" taxon
        Then the visitor should see "$100.00" as the price of the "T-Shirt" product in the "Web-US" channel
        And the visitor should see "$7.00" as the price of the "Mug" product in the "Web-US" channel
        And the visitor should still see "$10.00" as the original price of the "Mug" product in the "Web-US" channel
