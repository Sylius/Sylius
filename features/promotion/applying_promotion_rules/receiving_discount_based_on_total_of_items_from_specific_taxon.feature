@applying_promotion_rules
Feature: Receiving discount based on total of items from specific taxon
    In order to pay less while buying goods with required total from promoted taxon
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
    Scenario: Receiving discount on order while buying product from promoted taxon which fits price criteria
        Given the promotion gives "$20.00" off if order contains products classified as "T-Shirts" with a minimum value of "$50.00"
        When I add product "PHP T-Shirt" to the cart
        Then my cart total should be "$80.00"
        And my discount should be "-$20.00"

    @ui
    Scenario: Receiving no discount on order while buying product from different than promoted taxon
        Given the promotion gives "$10.00" off if order contains products classified as "T-Shirts" with a minimum value of "$15.00"
        When I add product "PHP Mug" to the cart
        Then my cart total should be "$20.00"
        And there should be no discount

    @ui
    Scenario: Receiving no discount on order while buying product from promoted taxon which not fits price criteria
        Given the promotion gives "$20.00" off if order contains products classified as "Mugs" with a minimum value of "$50.00"
        When I add product "PHP Mug" to the cart
        Then my cart total should be "$20.00"
        And there should be no discount

    @ui
    Scenario: Receiving discount on order while buying multiple products from promoted taxon which fit price criteria
        Given the promotion gives "$20.00" off if order contains products classified as "Mugs" with a minimum value of "$50.00"
        When I add 3 products "PHP Mug" to the cart
        Then my cart total should be "$40.00"
        And my discount should be "-$20.00"

    @ui
    Scenario: Receiving discount on order while buying products from both of promoted taxons which fits price criteria
        Given the promotion gives "$10.00" off if order contains products classified as "T-Shirts" with a minimum value of "$50.00"
        And there is a promotion "Mugs promotion"
        And the promotion gives "$5.00" off if order contains products classified as "Mugs" with a minimum value of "$30.00"
        When I add product "PHP T-Shirt" to the cart
        And I add 2 products "PHP Mug" to the cart
        Then my cart total should be "$125.00"
        And my discount should be "-$15.00"
