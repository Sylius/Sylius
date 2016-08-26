@receiving_discount
Feature: Receiving fixed discount on cart from coupon
    In order to pay proper amount while buying promoted goods and using proper coupon
    As a Visitor
    I want to have promotions applied to my cart

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$100.00"
        And the store has a product "PHP Mug" priced at "$6.00"
        And the store has promotion "Christmas sale" with coupon "SANTA2016"

    @ui
    Scenario: Receiving fixed discount for my cart
        Given this promotion gives "$10.00" discount to every order
        When I add product "PHP T-Shirt" to the cart
        And I use coupon with code "SANTA2016"
        Then I should be notified that promotion coupon has been added to the cart
        And my cart total should be "$90.00"
        And my discount should be "-$10.00"

    @ui
    Scenario: Receiving no discount from invalid coupon
        Given this promotion gives "$10.00" discount to every order
        When I add product "PHP T-Shirt" to the cart
        And I use coupon with code "SANTA2011"
        And my cart total should be "$100.00"
        And there should be no discount
