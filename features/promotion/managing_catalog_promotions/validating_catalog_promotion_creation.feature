@managing_catalog_promotions
Feature: Validating a catalog promotion creation
    In order to set up a catalog promotion with only valid data
    As an Administrator
    I want to be prevented from adding invalid catalog promotion

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a "T-Shirt" configurable product
        And this product has "PHP T-Shirt" variant priced at "$20.00"
        And this product has "Kotlin T-Shirt" variant priced at "$40.00"
        And this product has "Python T-Shirt" variant priced at "$40.00"
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

    @api
    Scenario: Trying to create a catalog promotion with invalid type of action
        When I want to create a new catalog promotion
        And I specify its code as "winter_sale"
        And I name it "Winter sale"
        And I make it available in channel "United States"
        And I specify its label as "Winter -50%" in "English (United States)"
        And I describe it as "This promotion gives a 50% discount on all products" in "English (United States)"
        And it applies on variants "PHP T-Shirt" variant and "Kotlin T-Shirt" variant
        And I specify catalog promotion action with nonexistent type
        And I try to add it
        Then I should be notified that type of action is invalid
        And there should be an empty list of catalog promotions

    @api
    Scenario: Trying to create a catalog promotion with not configured percentage discount action
        When I want to create a new catalog promotion
        And I specify its code as "winter_sale"
        And I name it "Winter sale"
        And I make it available in channel "United States"
        And I specify its label as "Winter -50%" in "English (United States)"
        And I describe it as "This promotion gives a 50% discount on all products" in "English (United States)"
        And it applies on variants "PHP T-Shirt" variant and "Kotlin T-Shirt" variant
        And I specify action percentage discount without amount configured
        And I try to add it
        Then I should be notified that a discount amount is required
        And there should be an empty list of catalog promotions

    @api
    Scenario: Trying to create a catalog promotion with wrong amount of percentage discount action
        When I want to create a new catalog promotion
        And I specify its code as "winter_sale"
        And I name it "Winter sale"
        And I make it available in channel "United States"
        And I specify its label as "Winter -50%" in "English (United States)"
        And I describe it as "This promotion gives a 50% discount on all products" in "English (United States)"
        And it applies on variants "PHP T-Shirt" variant and "Kotlin T-Shirt" variant
        And I specify action that gives "120%" percentage discount
        And I try to add it
        Then I should be notified that a discount amount should be between 0% and 100%
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
