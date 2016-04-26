@managing_promotions
Feature: Promotion validation
    In order to avoid making mistakes when managing a promotion
    As an Administrator
    I want to be prevented from adding it without specifying required fields

    Background:
        Given the store operates on a single channel in "France"
        And I am logged in as an administrator

    @ui
    Scenario: Trying to add a new promotion without specifying its code
        Given I want to create a new promotion
        When I name it "No-VAT promotion"
        And I do not specify its code
        And I try to add it
        Then I should be notified that code is required
        And promotion with name "No-VAT promotion" should not be added

    @ui
    Scenario: Trying to add a new promotion without specifying its name
        Given I want to create a new promotion
        When I specify its code as "no_vat_promotion"
        But I do not name it
        And I try to add it
        Then I should be notified that name is required
        And promotion with code "no_vat_promotion" should not be added

    @ui
    Scenario: Adding a promotion with start date set up after end date
        Given I want to create a new promotion
        When I specify its code as "FULL_METAL_PROMOTION"
        And I name it "Full metal promotion"
        And I make it available from "24.12.2017" to "12.12.2017"
        And I try to add it
        Then I should be notified that promotion cannot end before it start

    @ui
    Scenario: Trying to remove name from existing promotion
        Given there is a promotion "Christmas sale"
        And I want to modify this promotion
        When I remove its name
        And I try to save my changes
        Then I should be notified that name is required
        And this promotion should still be named "Christmas sale"

    @ui
    Scenario: Trying to add start later then end date for existing promotion
        Given there is a promotion "Christmas sale"
        And I want to modify this promotion
        And I make it available from "24.12.2017" to "12.12.2017"
        And I try to save my changes
        Then I should be notified that promotion cannot end before it start
