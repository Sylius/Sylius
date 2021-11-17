@applying_catalog_promotions
Feature: Applying catalog promotion with on promotions
    In order to have correct price of product
    As a Visitor
    I want to be able to buy product with cart promotion and catalog promotion

    Background:
        Given the store operates on a single channel in "United States"
        And the store classifies its products as "Clothes" and "Dishes"
        And the store has a "T-Shirt" configurable product
        And this product belongs to "Clothes"
        And this product has "PHP T-Shirt" variant priced at "$100.00"
        And the store has a "Pants" configurable product
        And this product belongs to "Clothes"
        And this product has "Aladdin Pants" variant priced at "$200.00"
        And there is a catalog promotion "Clothes sale" that reduces price by "30%" and applies on "Clothes" taxon
        And it applies also on "PHP T-Shirt" variant
        And there is a promotion "Holiday promotion"
        And the promotion gives "$10.00" discount to every order with quantity at least 1

    @api @ui
    Scenario: Applying cart promotion on discounted products
        When I add product "T-Shirt" to the cart
        Then my cart total should be "$60.00"

    @api @ui
    Scenario: Applying cart promotion on discounted and not discounted products
        When I add product "T-Shirt" to the cart
        And I add product "Pants" to the cart
        Then my cart total should be "$200.00"

    @api @ui
    Scenario: Not applying cart promotion that can not be applied on discounted products
        Given this promotion can not be applied on discounted products
        When I add product "T-Shirt" to the cart
        Then my cart total should be "$70.00"
