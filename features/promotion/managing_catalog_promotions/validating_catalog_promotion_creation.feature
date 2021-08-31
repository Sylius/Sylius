@managing_catalog_promotions
Feature: Validating a catalog promotion creation
    In order to set up a catalog promotion with only valid data
    As an Administrator
    I want to be prevented from adding invalid catalog promotion

    Background:
        Given the store operates on a single channel in "United States"
        And I am logged in as an administrator

    @api
    Scenario: Trying to create a catalog promotion with invalid rule
        When I want to create a new catalog promotion
        And I specify its code as "winter_sale"
        And I name it "Winter sale"
        And I specify its label as "Winter -50%" in "English (United States)"
        And I describe it as "This promotion gives a 50% discount on all products" in "English (United States)"
        And I specify nonexistent catalog promotion rule
        And I try to add it
        Then I should be notified that rule is invalid
        And there should be an empty list of catalog promotions

    @api
    Scenario: Trying to create a catalog promotion with rule with invalid configuration
        When I want to create a new catalog promotion
        And I specify its code as "winter_sale"
        And I name it "Winter sale"
        And I specify its label as "Winter -50%" in "English (United States)"
        And I describe it as "This promotion gives a 50% discount on all products" in "English (United States)"
        And I specify rule "For variants" with the wrong configuration
        And I try to add it
        Then I should be notified that rule configuration is invalid
        And there should be an empty list of catalog promotions

    @api @ui
    Scenario: Trying to create a catalog promotion without specifying its code and name
        When I create a new catalog promotion without specifying its code and name
        Then I should be notified that code and name are required
        And there should be an empty list of catalog promotions

    @api @ui
    Scenario: Trying to create a catalog promotion with taken code
        Given there is a catalog promotion with "sale" code and "Summer sale" name
        When I create a new catalog promotion with "sale" code and "Winter sale" name
        Then I should be notified that catalog promotion with this code already exists
        And there should still be only one catalog promotion with code "sale"
