@managing_promotion_coupons
Feature: Adding a new coupon
    In order to create coupons for my promotions
    As an Administrator
    I want to add a new coupon

    Background:
        Given the store operates on a single channel in "France"
        And there is a promotion "Christmas sale"
        And it is coupon based promotion
        And I am logged in as an administrator

    @ui
    Scenario: Adding a new coupon
        Given I want to create a new coupon for this promotion
        When I specify its code as "Santa"
        And I set its usage limit to 10
        And I set its per customer usage limit to 50
        And I make it available till "21.04.2017"
        And I add it
        Then I should be notified that it has been successfully created
        And I should see the coupon with code "Santa"
