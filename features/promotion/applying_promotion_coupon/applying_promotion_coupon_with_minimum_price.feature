@applying_promotion_coupon
Feature: Applying promotion coupon with minimum price specified
    In order to pay proper amount after using the promotion coupon with product with minimum price specified
    As a Visitor
    I want to have promotion coupon's discounts applied to my cart based on minimum price

    Background:
        Given the store operates on a single channel in "United States"
        And the store classifies its products as "T-Shirts"
        And the store has a "T-Shirt" configurable product
        And it belongs to "T-Shirts"
        And this product has "PHP T-Shirt" variant priced at "$20.00"
        And the "PHP T-Shirt" variant has minimum price "$15.00" in "United States" channel
        And the store has promotion "Christmas sale" with coupon "SANTA2016"
        And this promotion gives "50%" discount to every order

    @ui @api
    Scenario: Receiving fixed discount for my cart
        When I add product "T-Shirt" to the cart
        And I use coupon with code "SANTA2016"
        Then my cart total should be "$15.00"
        And my discount should be "-$5.00"

    @ui @api
    Scenario: Receiving discount from valid coupon with a usage limit
        Given this coupon can be used 5 times
        When I add product "T-Shirt" to the cart
        And I use coupon with code "SANTA2016"
        Then my cart total should be "$15.00"
        And my discount should be "-$5.00"
