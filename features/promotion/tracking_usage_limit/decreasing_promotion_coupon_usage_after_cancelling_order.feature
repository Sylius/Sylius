@managing_promotion_coupons
Feature: Decreasing a promotion coupon usage after cancelling an order
    In order to exclude cancelled orders from promotion coupon usage
    As an Administrator
    I want to have a promotion coupon usage decreased after order cancellation

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt"
        And the store ships everywhere for free
        And the store allows paying with "Cash on Delivery"
        And the store has promotion "Christmas sale" with coupon "SANTA2016"
        And I am logged in as an administrator

    @ui
    Scenario: Seeing promotion coupon usage decreased after order cancellation
        Given there is a customer "john.doe@gmail.com" that placed an order "#00000022"
        And the customer bought a single "PHP T-Shirt" using "SANTA2016" coupon
        And the customer chose "Free" shipping method to "United States" with "Cash on Delivery" payment
        But the customer cancelled this order
        When I browse all coupons of "Christmas sale" promotion
        Then "SANTA2016" coupon should be used 0 times

    @ui
    Scenario: Seeing promotion coupon usage decreased to 1 after second order cancellation
        Given there is a customer "john.doe@gmail.com" that placed an order "#00000022"
        And the customer bought a single "PHP T-Shirt" using "SANTA2016" coupon
        And the customer chose "Free" shipping method to "United States" with "Cash on Delivery" payment
        Given there is a customer "andrew.cole@gmail.com" that placed an order "#00000023"
        And the customer bought a single "PHP T-Shirt" using "SANTA2016" coupon
        And the customer chose "Free" shipping method to "United States" with "Cash on Delivery" payment
        And the customer cancelled this order
        When I browse all coupons of "Christmas sale" promotion
        Then "SANTA2016" coupon should be used 1 time
