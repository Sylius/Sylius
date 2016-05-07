@managing_promotions
Feature: Browsing promotion
    In order to see all promotion
    As an Administrator
    I want to browse coupons

    Background:
        Given the store operates on a single channel in "France"
        And there is a promotion "Basic promotion"
        And I am logged in as an administrator

    @ui
    Scenario: Browsing promotions
        Given I want to see all promotion
        Then I should see 1 promotions on the list
        And the "Basic promotion" promotion should appear in the registry

    @ui
    Scenario: Browsing manage button for coupon based promotion
        Given the store has promotion "Christmas sale" with coupon "Santa's gift"
        And I want to see all promotion
        Then this promotion should be coupon based
        And I should be able to manage those coupons for this promotion
