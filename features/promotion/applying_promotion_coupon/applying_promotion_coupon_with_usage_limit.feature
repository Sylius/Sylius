@applying_promotion_coupon
Feature: Applying promotion coupon with usage limit
    In order to pay proper amount after using the promotion coupon
    As a Visitor
    I want to have promotion coupon's discounts applied to my cart only if the given promotion coupon is valid

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$100.00"
        And the store has promotion "Christmas sale" with coupon "SANTA2016"
        And this promotion gives "$10.00" discount to every order

    @ui
    Scenario: Receiving discount from valid coupon with a usage limit
        Given this coupon can be used 5 times
        When I add product "PHP T-Shirt" to the cart
        And I use coupon with code "SANTA2016"
        Then my cart total should be "$90.00"
        And my discount should be "-$10.00"

    @ui
    Scenario: Receiving no discount from valid coupon that has reached its usage limit
        Given this coupon has already reached its usage limit
        When I add product "PHP T-Shirt" to the cart
        And I use coupon with code "SANTA2016"
        Then I should be notified that the coupon is invalid
        And my cart total should be "$100.00"
        And there should be no discount
