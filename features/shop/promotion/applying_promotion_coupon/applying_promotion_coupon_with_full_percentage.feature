@applying_promotion_coupon
Feature: Applying promotion coupon
    In order to pay proper amount after using the promotion coupon
    As a Visitor
    I want to have promotion coupon's discounts applied to my cart when it is a promotion that provides 100% discount

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$100.00"
        And the store has promotion "Christmas sale" with coupon "SANTA2016"
        And this promotion gives "100%" discount to every order

    @ui @api
    Scenario: Receiving full percentage discount for my cart
        When I add product "PHP T-Shirt" to the cart
        And I use coupon with code "SANTA2016"
        Then my cart total should be "$0.00"
        And my discount should be "-$100.00"
