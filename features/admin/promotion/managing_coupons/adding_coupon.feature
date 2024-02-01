@managing_promotion_coupons
Feature: Adding a new coupon
    In order to create coupons for my promotions
    As an Administrator
    I want to add a new coupon

    Background:
        Given the store operates on a single channel in "United States"
        And there is a promotion "Christmas sale"
        And it is coupon based promotion
        And I am logged in as an administrator

    @ui @api
    Scenario: Adding a new coupon
        When I want to create a new coupon for this promotion
        And I specify its code as "SANTA2016"
        And I limit its usage to 100 times
        And I limit its per customer usage to 50 times
        And I make it valid until "21.04.2017"
        And I add it
        Then I should be notified that it has been successfully created
        And there should be a "Christmas sale" promotion with a coupon code "SANTA2016"
