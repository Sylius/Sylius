@managing_promotion_coupons
Feature: Coupon validation
    In order to avoid making mistakes when managing a coupon
    As an Administrator
    I want to be prevented from adding incorrect coupons

    Background:
        Given the store operates on a single channel in "United States"
        And there is a promotion "Christmas sale"
        And it is coupon based promotion
        And I am logged in as an administrator

    @ui @api
    Scenario: Trying to add a new coupon without specifying its code
        When I want to create a new coupon for this promotion
        And I do not specify its code
        And I limit its usage to 30 times
        And I limit its per customer usage to 40 times
        And I make it valid until "26.03.2017"
        And I try to add it
        Then I should be notified that code is required
        And there should be 0 coupons related to this promotion

    @ui @api
    Scenario: Trying to add a new coupon with a too long code
        When I want to create a new coupon for this promotion
        And I limit its usage to 30 times
        And I limit its per customer usage to 40 times
        And I make it valid until "26.03.2017"
        And I specify a too long code
        And I try to add it
        Then I should be notified that code is too long

    @ui @api
    Scenario: Trying to add a new coupon with usage limit below one
        When I want to create a new coupon for this promotion
        And I specify its code as "SANTA2016"
        And I limit its usage to "-1" times
        And I limit its per customer usage to 25 times
        And I make it valid until "26.03.2017"
        And I try to add it
        Then I should be notified that coupon usage limit must be at least one
        And there should be 0 coupons related to this promotion

    @ui @api
    Scenario: Trying to add a new coupon with per customer usage limit below one
        When I want to create a new coupon for this promotion
        And I specify its code as "SANTA2016"
        And I limit its usage to 30 times
        And I limit its per customer usage to -1 times
        And I make it valid until "26.03.2017"
        And I try to add it
        Then I should be notified that coupon usage limit per customer must be at least one
        And there should be 0 coupons related to this promotion

    @api @no-ui
    Scenario: Trying to add a new coupon with no promotion
        When I want to create a new coupon
        And I specify its code as "RANDOM"
        And I limit its usage to 30 times
        And I limit its per customer usage to 3 times
        And I make it valid until "26.03.2017"
        And I try to add it
        Then I should be notified that promotion is required
        And there should be no coupon with code "RANDOM"

    @api @no-ui
    Scenario: Trying to add a new coupon for a non-coupon based promotion
        Given there is a promotion "Flash sale"
        When I want to create a new coupon for this promotion
        And I specify its code as "FAST-50"
        And I limit its usage to 50 times
        And I limit its per customer usage to 1 time
        And I make it valid until "26.03.2017"
        And I try to add it
        Then I should be notified that only coupon based promotions can have coupons
        And there should be 0 coupons related to this promotion
