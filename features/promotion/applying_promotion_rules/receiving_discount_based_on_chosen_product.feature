@applying_promotion_rules
Feature: Receiving discount based on chosen product
    In order to pay less while buying some goods
    As a Customer
    I want to receive discount for my purchase

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$100.00"
        And there is a promotion "T-Shirts promotion"
        And the promotion gives "$20.00" off if order contains a "PHP T-Shirt" product

    @ui
    Scenario: Receiving discount on order while buying promoted product
        When I add product "PHP T-Shirt" to the cart
        Then my cart total should be "$80.00"
        And my discount should be "-$20.00"

    @ui
    Scenario: Receiving discount on order while buying promoted product
        When I add 2 products "PHP T-Shirt" to the cart
        Then my cart total should be "$180.00"
        And my discount should be "-$20.00"

    @ui
    Scenario: Receiving no discount on order while buying product different then discounted
        Given the store has a product "PHP Mug" priced at "$20.00"
        When I add product "PHP Mug" to the cart
        Then my cart total should be "$20.00"
        And there should be no discount
