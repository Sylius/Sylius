@managing_promotion_coupons
Feature: Generating a new coupons
    In order to generate coupons for my promotions
    As an Administrator
    I want to add a new coupon

    Background:
        Given the store operates on a single channel in "France"
        And there is a promotion "Christmas sale"
        And it is coupon based promotion
        And I am logged in as an administrator

    @ui
    Scenario: Generating a new coupons
        Given I want to generate a new coupons for this promotion
        When I specify its amount as 5
        And I set generated coupons usage limit to 25
        And I make generated coupons available till "26.03.2017"
        And I generate it
        Then I should be notified that it has been successfully generated
        And I should see 5 coupon on the list related to this promotion
