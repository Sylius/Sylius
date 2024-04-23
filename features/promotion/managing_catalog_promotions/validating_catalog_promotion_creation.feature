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

    @api @no-ui
    Scenario: Trying to add catalog promotion with translation in unexisting locale
        When I want to create a new catalog promotion
        And I specify its code as "winter_sale"
        And I name it "Winter sale"
        And I specify its label as "Vente -50%" in "French (France)"
        And I save my changes
        Then I should be notified that the locale is not available

    @api @ui
    Scenario: Trying to create a catalog promotion without specifying its code and name
        When I create a new catalog promotion without specifying its code and name
        Then I should be notified that code and name are required
        And there should be an empty list of catalog promotions

    @api @ui
    Scenario: Trying to create a catalog promotion with a too long code
        When I want to create a new catalog promotion
        And I name it "Winter sale"
        And I specify its label as "Winter -50%" in "English (United States)"
        And I describe it as "This promotion gives a 50% discount on all products" in "English (United States)"
        And I specify a too long code
        And I try to add it
        Then I should be notified that code is too long

    @api @ui
    Scenario: Trying to create a catalog promotion with taken code
        Given there is a catalog promotion with "sale" code and "Summer sale" name
        When I create a new catalog promotion with "sale" code and "Winter sale" name
        Then I should be notified that catalog promotion with this code already exists
        And there should still be only one catalog promotion with code "sale"

    @api
    Scenario: Trying to create a catalog promotion with invalid type of scope
        When I want to create a new catalog promotion
        And I specify its code as "winter_sale"
        And I name it "Winter sale"
        And I specify its label as "Winter -50%" in "English (United States)"
        And I describe it as "This promotion gives a 50% discount on all products" in "English (United States)"
        And I add catalog promotion scope with nonexistent type
        And I try to add it
        Then I should be notified that type of scope is invalid
        And there should be an empty list of catalog promotions

    @api
    Scenario: Trying to create a catalog promotion with scope with invalid configuration
        When I want to create a new catalog promotion
        And I specify its code as "winter_sale"
        And I name it "Winter sale"
        And I specify its label as "Winter -50%" in "English (United States)"
        And I describe it as "This promotion gives a 50% discount on all products" in "English (United States)"
        And I add for variants scope with the wrong configuration
        And I try to add it
        Then I should be notified that scope configuration is invalid
        And there should be an empty list of catalog promotions

    @api @ui @javascript
    Scenario: Trying to create a catalog promotion with not configured for variants scope
        When I want to create a new catalog promotion
        And I specify its code as "winter_sale"
        And I name it "Winter sale"
        And I specify its label as "Winter -50%" in "English (United States)"
        And I describe it as "This promotion gives a 50% discount on all products" in "English (United States)"
        And I add for variants scope without variants configured
        And I try to add it
        Then I should be notified that at least 1 variant is required
        And there should be an empty list of catalog promotions

    @api
    Scenario: Trying to create a catalog promotion with invalid type of action
        When I want to create a new catalog promotion
        And I specify its code as "winter_sale"
        And I name it "Winter sale"
        And I make it available in channel "United States"
        And I specify its label as "Winter -50%" in "English (United States)"
        And I describe it as "This promotion gives a 50% discount on all products" in "English (United States)"
        And I add scope that applies on variants "PHP T-Shirt" variant and "Kotlin T-Shirt" variant
        And I add catalog promotion action with nonexistent type
        And I try to add it
        Then I should be notified that type of action is invalid
        And there should be an empty list of catalog promotions

    @api @ui @mink:chromedriver
    Scenario: Trying to create a catalog promotion with not configured percentage discount action
        When I want to create a new catalog promotion
        And I specify its code as "winter_sale"
        And I name it "Winter sale"
        And I make it available in channel "United States"
        And I specify its label as "Winter -50%" in "English (United States)"
        And I describe it as "This promotion gives a 50% discount on all products" in "English (United States)"
        And I add scope that applies on variants "PHP T-Shirt" variant and "Kotlin T-Shirt" variant
        And I add percentage discount action without amount configured
        And I try to add it
        Then I should be notified that the percentage amount should be a number and cannot be empty
        And there should be an empty list of catalog promotions

    @api @ui @mink:chromedriver
    Scenario: Trying to create a catalog promotion with wrong amount of percentage discount action
        When I want to create a new catalog promotion
        And I specify its code as "winter_sale"
        And I name it "Winter sale"
        And I make it available in channel "United States"
        And I specify its label as "Winter -50%" in "English (United States)"
        And I describe it as "This promotion gives a 50% discount on all products" in "English (United States)"
        And I add scope that applies on variants "PHP T-Shirt" variant and "Kotlin T-Shirt" variant
        And I add action that gives "120%" percentage discount
        And I try to add it
        Then I should be notified that a discount amount should be between 0% and 100%
        And there should be an empty list of catalog promotions

    @api @ui @mink:chromedriver
    Scenario: Trying to create a catalog promotion with wrong value of percentage discount action
        When I want to create a new catalog promotion
        And I specify its code as "winter_sale"
        And I name it "Winter sale"
        And I add scope that applies on variants "PHP T-Shirt" variant and "Kotlin T-Shirt" variant
        And I add invalid percentage discount action with non number in amount
        And I try to add it
        Then I should be notified that the percentage amount should be a number and cannot be empty
        And there should be an empty list of catalog promotions

    @api @ui @mink:chromedriver
    Scenario: Trying to create a catalog promotion with not configured fixed discount action
        When I want to create a new catalog promotion
        And I specify its code as "winter_sale"
        And I name it "Winter sale"
        And I make it available in channel "United States"
        And I specify its label as "Winter -50%" in "English (United States)"
        And I describe it as "This promotion gives a $10.00 discount on every product" in "English (United States)"
        And I add scope that applies on variants "PHP T-Shirt" variant and "Kotlin T-Shirt" variant
        And I add fixed discount action without amount configured for the "United States" channel
        And I try to add it
        Then I should be notified that the fixed amount should be a number and cannot be empty
        And there should be an empty list of catalog promotions

    @api
    Scenario: Trying to create a catalog promotion with wrong value of fixed discount action
        When I want to create a new catalog promotion
        And I specify its code as "winter_sale"
        And I name it "Winter sale"
        And I make it available in channel "United States"
        And I specify its label as "Winter -50%" in "English (United States)"
        And I describe it as "This promotion gives a $10.00 discount on every product" in "English (United States)"
        And I add scope that applies on variants "PHP T-Shirt" variant and "Kotlin T-Shirt" variant
        And I add invalid fixed discount action with non number in amount for the "United States" channel
        And I try to add it
        Then I should be notified that the fixed amount should be a number and cannot be empty
        And there should be an empty list of catalog promotions

    @api
    Scenario: Trying to create a catalog promotion with fixed discount action with invalid channel
        When I want to create a new catalog promotion
        And I specify its code as "winter_sale"
        And I name it "Winter sale"
        And I make it available in channel "United States"
        And I specify its label as "Winter -50%" in "English (United States)"
        And I describe it as "This promotion gives a $10.00 discount on every product" in "English (United States)"
        And I add scope that applies on variants "PHP T-Shirt" variant and "Kotlin T-Shirt" variant
        And I edit it to have "$10.00" of fixed discount in the "United States" channel
        And I add invalid fixed discount action configured for nonexistent channel
        And I try to add it
        Then I should be notified that at least one of the provided channel codes does not exist
        And there should be an empty list of catalog promotions

    @api @ui @javascript
    Scenario: Trying to create a catalog promotion with taxon type without taxons
        When I want to create a new catalog promotion
        And I specify its code as "winter_sale"
        And I name it "Winter sale"
        And I specify its label as "Winter -50%" in "English (United States)"
        And I describe it as "This promotion gives a 50% discount on all products" in "English (United States)"
        And I add catalog promotion scope for taxon without taxons
        And I try to add it
        Then I should be notified that I must add at least one taxon
        And there should be an empty list of catalog promotions

    @api
    Scenario: Trying to create a catalog promotion with taxon type with invalid taxons
        When I want to create a new catalog promotion
        And I specify its code as "winter_sale"
        And I name it "Winter sale"
        And I specify its label as "Winter -50%" in "English (United States)"
        And I describe it as "This promotion gives a 50% discount on all products" in "English (United States)"
        And I add catalog promotion scope for taxon with nonexistent taxons
        And I try to add it
        Then I should be notified that I can add only existing taxon
        And there should be an empty list of catalog promotions

    @api @ui @javascript
    Scenario: Trying to create a catalog promotion with product type without products
        When I want to create a new catalog promotion
        And I specify its code as "winter_sale"
        And I name it "Winter sale"
        And I specify its label as "Winter -50%" in "English (United States)"
        And I describe it as "This promotion gives a 50% discount on all products" in "English (United States)"
        And I add catalog promotion scope for product without products
        And I try to add it
        Then I should be notified that I must add at least one product
        And there should be an empty list of catalog promotions

    @api
    Scenario: Trying to create a catalog promotion with product type with invalid products
        When I want to create a new catalog promotion
        And I specify its code as "winter_sale"
        And I name it "Winter sale"
        And I specify its label as "Winter -50%" in "English (United States)"
        And I describe it as "This promotion gives a 50% discount on all products" in "English (United States)"
        And I add catalog promotion scope for product with nonexistent products
        And I try to add it
        Then I should be notified that I can add only existing product
