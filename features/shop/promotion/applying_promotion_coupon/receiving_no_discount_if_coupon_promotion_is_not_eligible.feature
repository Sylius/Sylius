@applying_promotion_coupon
Feature: Receiving no discount if coupon promotion is not eligible
    In order to be aware of not applied promotion on my cart
    As a Customer
    I want to be informed that coupon I want to apply is invalid

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$100.00"
        And the store has promotion "Christmas sale" with coupon "SANTA2016"
        And this promotion gives "$10.00" discount to every order with quantity at least 2
        And the store ships everywhere for Free
        And the store allows paying "Cash on Delivery"
        And I am a logged in customer

    @api @ui
    Scenario: Receiving no discount if promotion for the applied coupon is not enabled in the current channel
        Given this promotion is not available in any channel
        And I added 2 products "PHP T-Shirt" to the cart
        And I applied the coupon with code "SANTA2016"
        When I check the details of my cart
        And my cart total should be "$200.00"
        And there should be no discount applied

    @api @ui
    Scenario: Receiving no discount if promotion for the applied coupon has not started yet
        Given this promotion starts tomorrow
        And I added 2 products "PHP T-Shirt" to the cart
        And I applied the coupon with code "SANTA2016"
        When I check the details of my cart
        Then my cart total should be "$200.00"
        And there should be no discount applied

    @api @ui
    Scenario: Receiving no discount if promotion for the applied coupon has already expired
        Given this promotion has already expired
        And I added 2 products "PHP T-Shirt" to the cart
        And I applied the coupon with code "SANTA2016"
        When I check the details of my cart
        Then my cart total should be "$200.00"
        And there should be no discount applied

    @api @ui
    Scenario: Receiving no discount if promotion's usage for the applied coupon is already exceeded
        Given this promotion has usage limit equal to 100
        And this promotion usage limit is already reached
        And I added 2 products "PHP T-Shirt" to the cart
        And I applied the coupon with code "SANTA2016"
        When I check the details of my cart
        Then my cart total should be "$200.00"
        And there should be no discount applied

    @api @ui
    Scenario: Receiving no discount if promotion's rules for the applied coupon are not fulfilled
        Given I added product "PHP T-Shirt" to the cart
        And I applied the coupon with code "SANTA2016"
        When I check the details of my cart
        And my cart total should be "$100.00"
        And there should be no discount applied
