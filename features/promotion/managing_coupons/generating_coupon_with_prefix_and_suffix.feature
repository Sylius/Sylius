@managing_promotion_coupons
Feature: Generating a new coupons with prefix and suffi
    In order to quickly create specific number of tailored coupons for my promotions
    As an Administrator
    I want to be able to generate coupons for promotion with prefix and suffix

    Background:
        Given the store operates on a single channel in "United States"
        And there is a promotion "Christmas sale"
        And it is coupon based promotion
        And I am logged in as an administrator

    @ui
    Scenario: Generating new coupons with prefix
        Given I want to generate a new coupons for this promotion
        When I specify its amount as 5
        And I specify its prefix as "CHRISTMAS_"
        And I specify its code length as 6
        And I generate it
        Then I should be notified that it has been successfully generated
        And there should be 5 coupon related to this promotion
        And all of the coupons codes should be prefixed with "CHRISTMAS_"

    @ui
    Scenario: Generating new coupons with suffix
        Given I want to generate a new coupons for this promotion
        When I specify its amount as 5
        And I specify its suffix as "_CHRISTMAS"
        And I specify its code length as 6
        And I generate it
        Then I should be notified that it has been successfully generated
        And there should be 5 coupon related to this promotion
        And all of the coupons codes should be suffixed with "_CHRISTMAS"

    @ui
    Scenario: Generating new coupons with prefix and suffix
        Given I want to generate a new coupons for this promotion
        When I specify its amount as 5
        And I specify its prefix as "CHRISTMAS_"
        And I specify its suffix as "_SALE"
        And I specify its code length as 6
        And I generate it
        Then I should be notified that it has been successfully generated
        And there should be 5 coupon related to this promotion
        And all of the coupons codes should be prefixed with "CHRISTMAS_"
        And all of the coupons codes should be suffixed with "_SALE"
