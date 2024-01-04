@managing_promotions
Feature: Adding a new promotion
    In order to sell more by creating discount incentives for customers
    As an Administrator
    I want to add a new promotion

    Background:
        Given the store operates on a single channel in "United States"
        And I am logged in as an administrator

    @api @ui
    Scenario: Adding a new promotion
        When I want to create a new promotion
        And I specify its code as "FULL_METAL_PROMOTION"
        And I name it "Full metal promotion"
        And I add it
        Then I should be notified that it has been successfully created
        And the "Full metal promotion" promotion should appear in the registry

    @api @ui
    Scenario: Adding a new promotion with usage limit
        When I want to create a new promotion
        And I specify its code as "FULL_METAL_PROMOTION"
        And I name it "Full metal promotion"
        And I set its usage limit to 50
        And I add it
        Then I should be notified that it has been successfully created
        And the "Full metal promotion" promotion should be available to be used only 50 times

    @api @ui
    Scenario: Adding a new exclusive promotion
        When I want to create a new promotion
        And I specify its code as "FULL_METAL_PROMOTION"
        And I name it "Full metal promotion"
        And I set it as exclusive
        And I add it
        Then I should be notified that it has been successfully created
        And the "Full metal promotion" promotion should be exclusive

    @api @ui
    Scenario: Adding a new coupon based promotion
        When I want to create a new promotion
        And I specify its code as "FULL_METAL_PROMOTION"
        And I name it "Full metal promotion"
        And I make it coupon based
        And I add it
        Then I should be notified that it has been successfully created
        And the "Full metal promotion" promotion should be coupon based

    @api @ui
    Scenario: Adding a new channels promotion
        When I want to create a new promotion
        And I specify its code as "FULL_METAL_PROMOTION"
        And I name it "Full metal promotion"
        And I make it applicable for the "United States" channel
        And I add it
        Then I should be notified that it has been successfully created
        And the "Full metal promotion" promotion should be applicable for the "United States" channel

    @api @ui
    Scenario: Adding a promotion with start and end date
        When I want to create a new promotion
        And I specify its code as "FULL_METAL_PROMOTION"
        And I name it "Full metal promotion"
        And I make it available from "21.04.2017" to "21.05.2017"
        And I add it
        Then I should be notified that it has been successfully created

    @api @ui
    Scenario: Adding a promotion not applies to discounted by catalog promotion items
        When I want to create a new promotion
        And I specify its code as "FULL_METAL_PROMOTION"
        And I name it "Full metal promotion"
        And I set it as not applies to discounted by catalog promotion items
        And I add it
        Then I should be notified that it has been successfully created
        And the "Full metal promotion" promotion should not applies to discounted items
