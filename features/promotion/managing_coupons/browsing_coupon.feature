@managing_promotion_coupons
Feature: Browsing promotion coupons
    In order to see all promotion coupons
    As an Administrator
    I want to browse coupons of specific promotion

    Background:
        Given the store operates on a single channel in "United States"
        And the store has promotion "Christmas sale" with coupon "SANTA2016"
        And I am logged in as an administrator

    @ui
    Scenario: Browsing coupons in store
        Given I want to view all coupons of this promotion
        Then there should be 1 coupon related to this promotion
        And there should be coupon with code "SANTA2016"
