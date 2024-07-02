@managing_promotions
Feature: Accessing the coupons management from the promotion page
    In order to facilitate work with the management of coupons
    As an Administrator
    I want to access the coupons management directly from the promotion show page

    Background:
        Given the store operates on a single channel in "United States"
        And there is a promotion "Christmas sale"
        And it is a coupon based promotion
        And I am logged in as an administrator

    @ui @no-api
    Scenario: Being able to manage promotion's coupons
        When I modify a "Christmas sale" promotion
        And I want to manage this promotion coupons
        Then I should be on this promotion's coupons management page

    @ui @no-api
    Scenario: Add new promotion and not being able to manage promotion's coupons
        When I create a new promotion
        And I specify its code as "FULL_METAL_PROMOTION"
        And I name it "Full metal promotion"
        And I add it
        Then I should not be able to access coupons management page
