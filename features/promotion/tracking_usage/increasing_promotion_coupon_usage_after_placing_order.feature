@managing_promotion_coupons
Feature: Increasing a promotion coupon usage after placing an order
    In order to track usage of promotion coupon
    As an Administrator
    I want to have a promotion coupon usage increased after order placement

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt"
        And the store ships everywhere for free
        And the store allows paying with "Cash on Delivery"
        And the store has promotion "Christmas sale" with coupon "SANTA2016"
        And I am logged in as an administrator

    @ui
    Scenario: Seeing promotion coupon usage increased after order placement
        Given there is a customer "john.doe@gmail.com" that placed an order "#00000022"
        And the customer bought a single "PHP T-Shirt" using "SANTA2016" coupon
        And the customer chose "Free" shipping method to "United States" with "Cash on Delivery" payment
        When I browse all coupons of "Christmas sale" promotion
        Then "SANTA2016" coupon should be used 1 time

    @ui
    Scenario: Seeing promotion coupon usage increased correctly after few orders placement
        Given there is a customer "john.doe@gmail.com" that placed an order "#00000022"
        And the customer bought a single "PHP T-Shirt" using "SANTA2016" coupon
        And the customer chose "Free" shipping method to "United States" with "Cash on Delivery" payment
        Given there is a customer "andrew.cole@gmail.com" that placed an order "#00000023"
        And the customer bought a single "PHP T-Shirt" using "SANTA2016" coupon
        And the customer chose "Free" shipping method to "United States" with "Cash on Delivery" payment
        When I browse all coupons of "Christmas sale" promotion
        Then "SANTA2016" coupon should be used 2 times
