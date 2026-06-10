@applying_promotion_coupon
Feature: Not invalidating own coupon with items total rule
    In order to keep my legitimately applied discount while editing my cart
    As a Customer
    I want the items total rule to not count its own promotion's discount when revalidating the coupon

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$22.00"
        And the store has promotion "Black Friday" with coupon "BUGME10"
        And this promotion gives "$30.00" discount to every order with items total at least "$40.00"
        And I am a logged in customer

    @api @ui @mink:chromedriver
    Scenario: Keeping the discount after increasing quantity while the coupon stays eligible
        Given I added 2 products "PHP T-Shirt" to the cart
        When I check the details of my cart
        And I use coupon with code "BUGME10"
        And I change product "PHP T-Shirt" quantity to 3 in my cart
        Then my cart total should be "$36.00"
        And my discount should be "-$30.00"
