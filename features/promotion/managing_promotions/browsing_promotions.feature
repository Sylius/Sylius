@managing_promotions
Feature: Browsing promotions
    In order to see all promotion
    As an Administrator
    I want to browse existing promotions

    Background:
        Given the store operates on a single channel in "United States"
        And there is a promotion "Basic promotion"
        And I am logged in as an administrator

    @ui
    Scenario: Browsing promotions
        Given I want to browse promotions
        Then there should be 1 promotion
        And the "Basic promotion" promotion should exist in the registry

    @ui
    Scenario: Browsing manage button for coupon based promotion
        Given the store has promotion "Christmas sale" with coupon "Santa's gift"
        And I want to browse promotions
        Then this promotion should be coupon based
        And I should be able to manage coupons for this promotion
