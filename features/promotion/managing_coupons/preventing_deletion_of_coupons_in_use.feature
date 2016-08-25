@managing_promotion_coupons
Feature: Not being able to delete a coupon which is in use
    In order to maintain proper payment history
    As an Administrator
    I want to be prevented from deleting a used coupon

    Background:
        Given the store operates on a single channel in "United States"
        And the store ships everywhere for free
        And the store has a product "Jacket"
        And the store allows paying with "Cash on Delivery"
        And the store has promotion "Christmas sale" with coupon "SANTA2016"
        And there is a customer "john.doe@gmail.com" that placed an order "#00000022"
        And the customer bought a single "Jacket" using "SANTA2016" coupon
        And the customer chose "Free" shipping method to "United States" with "Cash on Delivery" payment
        And I am logged in as an administrator

    @domain @ui
    Scenario: Being unable to delete a used coupon
        When I try to delete "SANTA2016" coupon related to this promotion
        Then I should be notified that it is in use and cannot be deleted
        And this coupon should still exist in the registry
