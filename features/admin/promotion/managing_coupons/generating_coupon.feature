@managing_promotion_coupons
Feature: Generating new coupons
    In order to quickly create specific number of coupons for my promotions
    As an Administrator
    I want to be able to generate coupons for promotion

    Background:
        Given the store operates on a single channel in "United States"
        And there is a promotion "Christmas sale"
        And it is coupon based promotion
        And I am logged in as an administrator

    @api @ui
    Scenario: Generating a new coupons
        When I want to generate new coupons for this promotion
        And I choose the amount of 5 coupons to be generated
        And I specify their code length as 6
        And I limit generated coupons usage to 25 times
        And I make generated coupons valid until "26.03.2017"
        And I generate these coupons
        Then I should be notified that they have been successfully generated
        And there should be 5 coupons related to this promotion

    @api @ui
    Scenario: Generating new coupons without expiration date
        When I want to generate new coupons for this promotion
        And I choose the amount of 5 coupons to be generated
        And I specify their code length as 6
        And I limit generated coupons usage to 25 times
        And I generate these coupons
        Then I should be notified that they have been successfully generated
        And there should be 5 coupons related to this promotion

    @api @ui
    Scenario: Generating new coupons with a large long code length value
        When I want to generate new coupons for this promotion
        And I choose the amount of 10 coupons to be generated
        And I specify their code length as 40
        And I generate these coupons
        Then I should be notified that they have been successfully generated
        And there should be 10 coupons related to this promotion
