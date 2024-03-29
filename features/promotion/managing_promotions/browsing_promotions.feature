@managing_promotions
Feature: Browsing promotions
    In order to see all promotion
    As an Administrator
    I want to browse existing promotions

    Background:
        Given the store operates on a single channel in "United States"
        And there is a promotion "Basic promotion"
        And I am logged in as an administrator

    @api @ui
    Scenario: Browsing promotions
        When I want to browse promotions
        Then I should see a single promotion in the list
        And the "Basic promotion" promotion should exist in the registry

    @api @ui
    Scenario: Browsing manage button for coupon based promotion
        Given the store has promotion "Christmas sale" with coupon "Santa's gift"
        When I want to browse promotions
        Then this promotion should be coupon based
        And I should be able to manage coupons for this promotion
