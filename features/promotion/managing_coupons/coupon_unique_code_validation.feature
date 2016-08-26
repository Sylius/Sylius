@managing_promotion_coupons
Feature: Coupon unique code validation
    In order to uniquely identify coupons
    As an Administrator
    I want to be prevented from adding two coupons with the same code

    Background:
        Given the store operates on a single channel in "United States"
        And the store has promotion "Christmas sale" with coupon "SANTA2016"
        And I am logged in as an administrator

    @ui
    Scenario: Trying to add coupon with taken code
        Given I want to create a new coupon for this promotion
        When I specify its code as "SANTA2016"
        And I limit its usage to 30 times
        And I limit its per customer usage to 50 times
        And I make it valid until "26.03.2017"
        And I try to add it
        Then I should be notified that coupon with this code already exists
        And there should still be only one coupon with code "SANTA2016" related to this promotion
