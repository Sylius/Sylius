@applying_promotion_coupon
Feature: Removing promotion coupon
    In order to be able to make my mind if I want to use the promotion code or not
    As a Customer
    I want to have possibility to remove once requested coupon code

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$100.00"
        And the store has promotion "Christmas sale" with coupon "SANTA2016"
        And this promotion gives "$10.00" discount to every order
        And I am a logged in customer

    @api @ui @mink:chromedriver
    Scenario: Removing coupon code from cart
        Given I added product "PHP T-Shirt" to the cart
        And this cart has promotion applied with coupon "SANTA2016"
        When I check details of my cart
        And I remove coupon from my cart
        Then my cart total should be "$100.00"
        And there should be no discount applied
