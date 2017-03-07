@managing_promotions
Feature: Promotion unique code validation
    In order to uniquely identify promotions
    As an Administrator
    I want to be prevented from adding two promotions with the same code

    Background:
        Given the store operates on a single channel in "United States"
        And there is a promotion "No-VAT promotion" identified by "NO_VAT" code
        And I am logged in as an administrator

    @ui
    Scenario: Trying to add promotion with taken code
        Given I want to create a new promotion
        When I specify its code as "NO_VAT"
        And I name it "No VAT promotion"
        And I try to add it
        Then I should be notified that promotion with this code already exists
        And there should still be only one promotion with code "NO_VAT"
