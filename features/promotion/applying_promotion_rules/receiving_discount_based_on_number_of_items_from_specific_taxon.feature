@applying_promotion_rules
Feature: Receiving a discount based on the number of products from a specific taxon
    In order to pay less while buying required number of goods from a promoted taxon
    As a Customer
    I want to receive a discount for my purchase

    Background:
        Given the store operates on a single channel in "France"
        And the store classifies its products as "T-Shirts" and "Mugs"
        And the store has a product "PHP T-Shirt" priced at "€100.00"
        And this product belongs to "T-Shirts"
        And the store has a product "Symfony T-Shirt" priced at "€150.00"
        And this product belongs to "T-Shirts"
        And the store has a product "PHP Mug" priced at "€20.00"
        And this product belongs to "Mugs"
        And there is a promotion "T-Shirts promotion"

    @ui
    Scenario: Receiving a discount on order while buying the required number of products from a promoted taxon
        Given the promotion gives "€50.00" off if order contains 2 products classified as "T-Shirts"
        When I add 3 products "PHP T-Shirt" to the cart
        Then my cart total should be "€250.00"
        And my discount should be "-€50.00"

    @ui
    Scenario: Receiving a discount on order while buying exactly the required number of products from a promoted taxon
        Given the promotion gives "€50.00" off if order contains 2 products classified as "T-Shirts"
        When I add 2 products "PHP T-Shirt" to the cart
        Then my cart total should be "€150.00"
        And my discount should be "-€50.00"

    @ui
    Scenario: Receiving no discount on order while buying less than required number of products from a promoted taxon
        Given the promotion gives "€50.00" off if order contains 2 products classified as "T-Shirts"
        When I add product "PHP T-Shirt" to the cart
        Then my cart total should be "€100.00"
        And there should be no discount

    @ui
    Scenario: Receiving a discount on order while buying multiple items with products from a promoted taxon which fit number criteria
        Given the promotion gives "€100.00" off if order contains 4 products classified as "T-Shirts"
        When I add 3 products "PHP T-Shirt" to the cart
        And I add 2 products "Symfony T-Shirt" to the cart
        Then my cart total should be "€500.00"
        And my discount should be "-€100.00"

    @ui
    Scenario: Receiving different discounts on different promotions checking number of products from the specific taxon
        Given the promotion gives "€50.00" off if order contains 2 products classified as "T-Shirts"
        And there is a promotion "Mugs promotion"
        And it gives "€10.00" off if order contains 3 products classified as "Mugs"
        When I add 3 products "PHP T-Shirt" to the cart
        And I add 3 products "PHP Mug" to the cart
        Then my cart total should be "€300.00"
        And my discount should be "-€60.00"
