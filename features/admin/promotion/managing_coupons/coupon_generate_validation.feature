@managing_promotion_coupons
Feature: Coupon generate instruction validation
    In order to avoid making mistakes when generating coupons for my promotions
    As an Administrator
    I want to be prevented from generating it without specifying required fields

    Background:
        Given the store operates on a single channel in "United States"
        And there is a promotion "Christmas sale"
        And it is coupon based promotion
        And I am logged in as an administrator

    @api @ui
    Scenario: Trying to generate new coupons without specifying their amount
        When I want to generate new coupons for this promotion
        And I do not specify its amount
        And I specify their code length as 6
        And I limit generated coupons usage to 25 times
        And I make generated coupons valid until "26.03.2017"
        And I try to generate these coupons
        Then I should be notified that generate amount is required
        And there should be 0 coupon related to this promotion

    @api @ui
    Scenario: Trying to generate new coupons without specifying their code length
        When I want to generate new coupons for this promotion
        And I do not specify their code length
        And I choose the amount of 4 coupons to be generated
        And I limit generated coupons usage to 25 times
        And I make generated coupons valid until "26.03.2017"
        And I try to generate these coupons
        Then I should be notified that generate code length is required
        And there should be 0 coupon related to this promotion

    @api @ui
    Scenario: Trying to generate new coupons with code length impossible to generate
        When I want to generate new coupons for this promotion
        And I specify their code length as 50
        And I choose the amount of 4 coupons to be generated
        And I limit generated coupons usage to 25 times
        And I make generated coupons valid until "26.03.2017"
        And I try to generate these coupons
        Then I should be notified that generate code length is out of range
        And there should be 0 coupon related to this promotion

    @api @ui
    Scenario: Trying to generate new coupons with amount and code length impossible to generate
        When I want to generate new coupons for this promotion
        And I specify their code length as 1
        And I choose the amount of 20 coupons to be generated
        And I limit generated coupons usage to 25 times
        And I make generated coupons valid until "26.03.2017"
        And I try to generate these coupons
        Then I should be notified that generating 20 coupons with code length equal to 1 is not possible
        And there should be 0 coupon related to this promotion
