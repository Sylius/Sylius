@applying_catalog_promotions
Feature: Applying catalog promotions in proper time range
    In order to be attracted to products
    As a Visitor
    I want to see products discounted by currently available promotions

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a "T-Shirt" configurable product
        And this product has "PHP T-Shirt" variant priced at "$20.00"
        And there is a catalog promotion "Winter sale" that reduces price by "30%" and applies on "PHP T-Shirt" variant

    @api @ui
    Scenario: Applying catalog promotion with its time range
        Given it is "2021-12-25" now
        And the catalog promotion "Winter sale" operates between "2021-12-20" and "2021-12-30"
        When I view product "T-Shirt"
        Then I should see the product price "$14.00"
        And I should see the product original price "$20.00"

    @api @ui
    Scenario: Not applying catalog promotion if its start date has not been reached
        Given it is "2021-12-15" now
        And the catalog promotion "Winter sale" operates between "2021-12-20" and "2021-12-30"
        When I view product "T-Shirt"
        Then I should see the product price "$20.00"
        And I should see this product has no catalog promotion applied

    @api @ui
    Scenario: Not applying catalog promotion if its end date has already passed
        Given it is "2022-01-01" now
        And the catalog promotion "Winter sale" operates between "2021-12-20" and "2021-12-30"
        When I view product "T-Shirt"
        Then I should see the product price "$20.00"
        And I should see this product has no catalog promotion applied

    @api @ui
    Scenario: Not applying catalog promotion if its end date has already passed after end date modification
        Given it is "2021-12-25" now
        And the catalog promotion "Winter sale" operates between "2021-12-20" and "2021-12-30"
        And the end date of catalog promotion "Winter sale" was changed to "2021-12-23"
        When I view product "T-Shirt"
        Then I should see the product price "$20.00"
