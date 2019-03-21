@managing_promotions
Feature: Seeing list coupons button if promotion has coupon based
    In order to facilitate work with the management of coupons
    As an Administrator
    I want to be prevented from manage coupons without specifying required fields

    Background:
        Given the store operates on a single channel in "United States"
        And there is a promotion "Christmas sale"
        And it is coupon based promotion
        And I am logged in as an administrator

    @ui
    Scenario: Edit promotion and seeing the button "Manage coupons"
        Given I want to modify a "Christmas sale" promotion
        Then I should be able to manage coupons in edit page for this promotioni

    @ui
    Scenario: Add new promotion and dont seeing the button "Manage coupons"
        Given I want to create a new promotion
        When I specify its code as "FULL_METAL_PROMOTION"
        And I name it "Full metal promotion"
        And I add it
        Then I should be not able to manage coupons in edit page for this promotion
