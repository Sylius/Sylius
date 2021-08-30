@managing_catalog_promotions
Feature: Creating a catalog promotion
    In order to set up a catalog promotion for some special occasions
    As an Administrator
    I want to have an option to configure such a promotion

    Background:
        Given the store operates on a single channel in "United States"
        And I am logged in as an administrator

    @api @ui
    Scenario: Creating a simple catalog promotion only with code and name
        When I create a new catalog promotion with "winter_sale" code and "Winter sale" name
        Then there should be 1 new catalog promotion on the list
        And it should have "winter_sale" code and "Winter sale" name
        And this catalog promotion should be usable

    @api
    Scenario: Creating a catalog promotion
        Given the store has a "T-Shirt" configurable product
        And this product has "PHP T-Shirt" variant priced at "$20.00"
        And this product has "Kotlin T-Shirt" variant priced at "$40.00"
        And this product has "Python T-Shirt" variant priced at "$40.00"
        When I want to create a new catalog promotion
        And I specify its code as "winter_sale"
        And I name it "Winter sale"
        And I specify its label as "Winter -50%" in "English (United States)"
        And I describe it as "This promotion gives a 50% discount on all products" in "English (United States)"
        And it applies on variants "PHP T-Shirt" variant and "Kotlin T-Shirt" variant
        And I add it
        Then there should be 1 new catalog promotion on the list
        And it should have "winter_sale" code and "Winter sale" name
        And it should apply to "PHP T-Shirt" variant and "Kotlin T-Shirt" variant
        And this catalog promotion should be usable

    @api
    Scenario: Creating a catalog promotion for channel
        When I want to create a new catalog promotion
        And I specify its code as "winter_sale"
        And I name it "Winter sale"
        And I make it available in channel "United States"
        And I add it
        Then there should be 1 new catalog promotion on the list
        And it should have "winter_sale" code and "Winter sale" name
        And the catalog promotion "Winter sale" should be available in channel "United States"
        And this catalog promotion should be usable

    @api
    Scenario: Trying to create a catalog promotion without specifying its code and name
        When I create a new catalog promotion without specifying its code and name
        Then I should be notified that code and name are required
        And there should be an empty list of catalog promotions

    @api
    Scenario: Trying to create a catalog promotion with taken code
        Given there is a catalog promotion with "sale" code and "Summer sale" name
        When I create a new catalog promotion with "sale" code and "Winter sale" name
        Then I should be notified that catalog promotion with this code already exists
        And there should still be only one catalog promotion with code "sale"
