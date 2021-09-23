@applying_catalog_promotions
Feature: Applying all available catalog promotions
    In order to be attracted to products
    As a Shop Owner
    I want to see discounted products in the catalog

    Background:
        Given the store operates on a channel named "Web-US"
        And the store has "Clothes" taxonomy
        And the store has "Dishes" taxonomy
        And the store has a "T-Shirt" configurable product
        And this product main taxon should be "Clothes"
        And this product has "PHP T-Shirt" variant priced at "$20.00" in "Web-US" channel
        And the store has a "Mug" configurable product
        And this product main taxon should be "Clothes"
        And this product has "Coffee Mug" variant priced at "$5.00" in "Web-US" channel
        And there is a catalog promotion "Clothes sale" that reduces price by "30%" and applies on "Clothes" taxonomy

    @todo
    Scenario: Applying multiple catalog promotions
        And there is a catalog promotion "Winter sale" available in "Web-US" channel that reduces price by "30%" and applies on "PHP T-shirt" variant
        And customer view shop on "Web-US" channel
        And customer view product "T-Shirt"
        Then customer should see the product price "$9.80"
        And customer should see the product original price "$20.00"

    @todo
    Scenario: Not applying catalog promotion if it's not eligible
        And customer view shop on "Web-US" channel
        When customer view product "Mug"
        Then customer should see the product price "$9.80"
        And customer should see the product original price "$20.00"
