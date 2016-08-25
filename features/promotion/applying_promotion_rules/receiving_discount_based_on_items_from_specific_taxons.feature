@applying_promotion_rules
Feature: Receiving discount based on products from specific taxon
    In order to pay less while buying goods from promoted taxons
    As a Customer
    I want to receive discount for my purchase

    Background:
        Given the store operates on a single channel in "United States"
        And the store classifies its products as "T-Shirts" and "Mugs"
        And the store has a product "PHP T-Shirt" priced at "$100.00"
        And it belongs to "T-Shirts"
        And the store has a product "PHP Mug" priced at "$20.00"
        And it belongs to "Mugs"
        And there is a promotion "T-Shirts promotion"

    @ui
    Scenario: Receiving discount on order while buying product from promoted taxon
        Given the promotion gives "$20.00" off if order contains products classified as "T-Shirts"
        When I add product "PHP T-Shirt" to the cart
        And my cart total should be "$80.00"
        And my discount should be "-$20.00"

    @ui
    Scenario: Receiving no discount on order while buying product from different than promoted taxon
        Given the promotion gives "$20.00" off if order contains products classified as "T-Shirts"
        When I add product "PHP Mug" to the cart
        And my cart total should be "$20.00"
        And there should be no discount

    @ui
    Scenario: Receiving discount on order while buying product from one of promoted taxon
        Given the promotion gives "$20.00" off if order contains products classified as "T-Shirts" or "Mugs"
        When I add product "PHP T-Shirt" to the cart
        And my cart total should be "$80.00"
        And my discount should be "-$20.00"

    @ui
    Scenario: Receiving discount on order while buying multiple products from promoted taxon
        Given the promotion gives "$50.00" off if order contains products classified as "T-Shirts"
        When I add 3 products "PHP T-Shirt" to the cart
        And my cart total should be "$250.00"
        And my discount should be "-$50.00"

    @ui
    Scenario: Receiving discount on order while buying product from both of promoted taxon
        Given the promotion gives "$10.00" off if order contains products classified as "T-Shirts"
        And there is a promotion "Mugs promotion"
        And this promotion gives "$5.00" off if order contains products classified as "Mugs"
        When I add product "PHP T-Shirt" to the cart
        And I add product "PHP Mug" to the cart
        And my cart total should be "$105.00"
        And my discount should be "-$15.00"
