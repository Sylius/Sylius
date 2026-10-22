@managing_promotion_coupons
Feature: Not increasing a promotion coupon usage with disabled track usage after placing an order
    In order to allow unlimited promotion coupon usage tracking
    As an Administrator
    I want to have a promotion coupon usage not increased after order placement when track usage is disabled

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a product "PHP T-Shirt"
        And the store ships everywhere for Free
        And the store allows paying with "Cash on Delivery"
        And the store has promotion "Christmas sale" with coupon "SANTA2016"
        And this coupon has track usage disabled
        And I am logged in as an administrator

    @api @ui
    Scenario: Seeing promotion coupon usage not increased after order placement when track usage is disabled
        Given there is a customer "john.doe@gmail.com" that placed an order "#00000022"
        And the customer bought a single "PHP T-Shirt" using "SANTA2016" coupon
        And the customer chose "Free" shipping method to "United States" with "Cash on Delivery" payment
        When I browse all coupons of "Christmas sale" promotion
        Then "SANTA2016" coupon should be used 0 times

    @api @ui
    Scenario: Seeing promotion coupon usage not increased correctly after few orders placement when track usage is disabled
        Given there is a customer "john.doe@gmail.com" that placed an order "#00000022"
        And the customer bought a single "PHP T-Shirt" using "SANTA2016" coupon
        And the customer chose "Free" shipping method to "United States" with "Cash on Delivery" payment
        Given there is a customer "andrew.cole@gmail.com" that placed an order "#00000023"
        And the customer bought a single "PHP T-Shirt" using "SANTA2016" coupon
        And the customer chose "Free" shipping method to "United States" with "Cash on Delivery" payment
        When I browse all coupons of "Christmas sale" promotion
        Then "SANTA2016" coupon should be used 0 times
