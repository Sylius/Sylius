@managing_promotion_coupons
Feature: Coupon unique code validation
    In order to uniquely identify coupons
    As an Administrator
    I want to be prevented from adding two coupons with the same code

    Background:
        Given the store operates on a single channel in "France"
        And the store has promotion "Christmas sale" with coupon "Santa's gift"
        And I am logged in as an administrator

    @ui
    Scenario: Trying to add coupon with taken code
        Given I want to create a new coupon for this promotion
        When I specify its code as "Santa's gift"
        And I set its usage limit to 30
        And I set its per customer usage limit to 25
        And I make it available till "26.03.2017"
        And I try to add it
        Then I should be notified that coupon with this code already exists
        And there should still be only one coupon with code "Santa's gift" related to this promotion
