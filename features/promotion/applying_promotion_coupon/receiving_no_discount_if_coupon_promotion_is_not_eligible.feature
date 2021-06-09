@applying_promotion_coupon
Feature: Receiving no discount if coupon promotion is not eligible
    In order to be aware of not applied promotion on my cart
    As a Customer
    I want to be informed that coupon I want to apply is invalid

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt" priced at "$100.00"
        And the store has promotion "Christmas sale" with coupon "SANTA2016"
        And this promotion gives "$10.00" discount to every order
        And the store ships everywhere for free
        And the store allows paying "Cash on Delivery"
        And I am a logged in customer

    @api
    Scenario: Receiving no discount if promotion for the applied coupon is not enabled in the current channel
        Given this promotion is not available in any channel
        When I add product "PHP T-Shirt" to the cart
        And I use coupon with code "SANTA2016"
        Then I should be notified that the promotion is invalid
        And my cart total should be "$100.00"

    @api
    Scenario: Receiving no discount if promotion for the applied coupon has not started yet
        Given this promotion starts tomorrow
        When I add product "PHP T-Shirt" to the cart
        And I use coupon with code "SANTA2016"
        Then I should be notified that the promotion is invalid
        And my cart total should be "$100.00"

    @api
    Scenario: Receiving no discount if promotion for the applied coupon has already expired
        Given this promotion has already expired
        When I add product "PHP T-Shirt" to the cart
        And I use coupon with code "SANTA2016"
        Then I should be notified that the promotion is invalid
        And my cart total should be "$100.00"
