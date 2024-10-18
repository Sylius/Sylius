@managing_promotion_coupons
Feature: Generating new coupons with prefix and suffix
    In order to quickly create specific number of tailored coupons for my promotions
    As an Administrator
    I want to be able to generate promotion coupons with prefix and suffix

    Background:
        Given the store operates on a single channel in "United States"
        And there is a promotion "Christmas sale"
        And it is coupon based promotion
        And I am logged in as an administrator

    @api @ui
    Scenario: Generating new coupons with prefix
        When I want to generate new coupons for this promotion
        And I choose the amount of 5 coupons to be generated
        And I specify their prefix as "CHRISTMAS_"
        And I specify their code length as 6
        And I generate these coupons
        Then I should be notified that they have been successfully generated
        And there should be 5 coupons related to this promotion
        And all of the coupon codes should be prefixed with "CHRISTMAS_"

    @api @ui
    Scenario: Generating new coupons with suffix
        When I want to generate new coupons for this promotion
        And I choose the amount of 5 coupons to be generated
        And I specify their suffix as "_CHRISTMAS"
        And I specify their code length as 6
        And I generate these coupons
        Then I should be notified that they have been successfully generated
        And there should be 5 coupons related to this promotion
        And all of the coupon codes should be suffixed with "_CHRISTMAS"

    @api @ui
    Scenario: Generating new coupons with prefix and suffix
        When I want to generate new coupons for this promotion
        And I choose the amount of 5 coupons to be generated
        And I specify their prefix as "CHRISTMAS_"
        And I specify their suffix as "_SALE"
        And I specify their code length as 6
        And I generate these coupons
        Then I should be notified that they have been successfully generated
        And there should be 5 coupons related to this promotion
        And all of the coupon codes should be prefixed with "CHRISTMAS_"
        And all of the coupon codes should be suffixed with "_SALE"
