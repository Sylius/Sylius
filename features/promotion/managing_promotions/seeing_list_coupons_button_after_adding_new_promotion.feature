@managing_promotions
Feature: Adding a new promotion
    In order to sell more by creating discount incentives for customers
    As an Administrator
    I want to add a new promotion

    Background:
        Given the store operates on a single channel in "United States"
        And I am logged in as an administrator

    @ui
    Scenario: Adding a new promotion and seeing the button "List coupons"
        Given I want to create a new promotion
        When I specify its code as "FULL_METAL_PROMOTION"
        And I name it "Full metal promotion"
        And I add it
        And I should see "List coupons"
