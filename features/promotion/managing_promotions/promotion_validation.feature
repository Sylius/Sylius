@managing_promotions
Feature: Promotion validation
    In order to avoid making mistakes when managing a promotion
    As an Administrator
    I want to be prevented from adding it without specifying required fields

    Background:
        Given I am logged in as an administrator

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
