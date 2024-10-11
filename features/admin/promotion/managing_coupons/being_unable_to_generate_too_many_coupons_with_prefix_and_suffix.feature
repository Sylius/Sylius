@managing_promotion_coupons
Feature: Being unable to generate too many coupons with prefix and suffix
    In order to avoid generating duplicated coupons for my promotions
    As an Administrator
    I want to be prevented from generating too many coupons with the same prefix or suffix

    Rules:
        - Generated coupon code uses 16 characters (numbers from 0 to 9, letters from A to F)
        - To prevent guessing coupons codes, it is not allowed to generate more than 50% of possible coupons

    Background:
        Given the store operates on a single channel in "United States"
        And there is a promotion "Christmas sale"
        And it is coupon based promotion
        And I am logged in as an administrator

    @api @ui
    Scenario: Being unable to generate too many coupons with prefix
        Given I have generated 8 coupons for this promotion with code length 1 and prefix "CHRISTMAS_"
        When I want to generate new coupons for this promotion
        And I choose the amount of 2 coupons to be generated
        And I specify their prefix as "CHRISTMAS_"
        And I specify their code length as 1
        And I try to generate these coupons
        Then I should be notified that generating 2 coupons with code length equal to 1 is not possible
        And there should still be 8 coupons related to this promotion

    @api @ui
    Scenario: Being unable to generate too many coupons with suffix
        Given I have generated 8 coupons for this promotion with code length 1 and suffix "_CHRISTMAS"
        When I want to generate new coupons for this promotion
        And I choose the amount of 2 coupons to be generated
        And I specify their suffix as "_CHRISTMAS"
        And I specify their code length as 1
        And I try to generate these coupons
        Then I should be notified that generating 2 coupons with code length equal to 1 is not possible
        And there should still be 8 coupons related to this promotion

    @api @ui
    Scenario: Being unable to generate too many coupons with prefix and suffix
        Given I have generated 8 coupons for this promotion with code length 1, prefix "CHRISTMAS_" and suffix "_SALE"
        When I want to generate new coupons for this promotion
        And I choose the amount of 2 coupons to be generated
        And I specify their prefix as "CHRISTMAS_"
        And I specify their suffix as "_SALE"
        And I specify their code length as 1
        And I try to generate these coupons
        Then I should be notified that generating 2 coupons with code length equal to 1 is not possible
        And there should still be 8 coupons related to this promotion
