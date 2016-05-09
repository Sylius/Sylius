@managing_promotion_coupons
Feature: Browsing promotion coupons
    In order to see all promotion coupons
    As an Administrator
    I want to browse coupons

    Background:
        Given the store operates on a single channel in "France"
        And the store has promotion "Christmas sale" with coupon "Santa's gift"
        And I am logged in as an administrator

    @ui
    Scenario: Browsing coupons in store
        Given I want to see all related coupons to this promotion
        Then I should see 1 coupon on the list related to this promotion
        And I should see the coupon with code "Santa's gift"
