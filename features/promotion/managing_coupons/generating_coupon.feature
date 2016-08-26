@managing_promotion_coupons
Feature: Generating a new coupons
    In order to quickly create specific number of coupons for my promotions
    As an Administrator
    I want to be able to generate coupons for promotion

    Background:
        Given the store operates on a single channel in "United States"
        And there is a promotion "Christmas sale"
        And it is coupon based promotion
        And I am logged in as an administrator

    @ui
    Scenario: Generating a new coupons
        Given I want to generate a new coupons for this promotion
        When I specify its amount as 5
        And I specify its code length as 6
        And I limit generated coupons usage to 25 times
        And I make generated coupons valid until "26.03.2017"
        And I generate it
        Then I should be notified that it has been successfully generated
        And there should be 5 coupon related to this promotion
