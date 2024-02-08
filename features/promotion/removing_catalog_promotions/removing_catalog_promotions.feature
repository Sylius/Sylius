@removing_catalog_promotions
Feature: Removing a catalog promotions
    In order to be in control of possible catalog promotions that exist on the system
    As an Administrator
    I want to have an option to remove such a promotion

    Background:
        Given the store operates on a single channel in "United States"
        And the store has "Clothes" taxonomy
        And the store has a "T-Shirt" configurable product
        And this product belongs to "Clothes"
        And this product has "PHP T-Shirt" variant priced at "$20.00"
        And there is a catalog promotion "Winter Sale" that reduces price by "30%" and applies on "Clothes" taxon
        And I am logged in as an administrator

    @api @ui
    Scenario: Removing an expired catalog promotion
        Given it is "2022-08-22" now
        And the catalog promotion "Winter Sale" operates between "2021-12-20" and "2021-12-30"
        When I request the removal of "Winter Sale" catalog promotion
        Then I should be notified that the removal operation has started successfully
        And there should be an empty list of catalog promotions
        And "PHP T-Shirt" variant should not be discounted

    @api @ui
    Scenario: Removing an active catalog promotion without any time limits
        When I request the removal of "Winter Sale" catalog promotion
        Then I should be notified that the removal operation has started successfully
        And there should be an empty list of catalog promotions
        And "PHP T-Shirt" variant should not be discounted

    @api @ui
    Scenario: Removing an active catalog promotion in the time range
        Given it is "2022-12-15" now
        And the catalog promotion "Winter Sale" operates between "2022-12-01" and "2022-12-30"
        When I request the removal of "Winter Sale" catalog promotion
        Then I should be notified that the removal operation has started successfully
        And there should be an empty list of catalog promotions
        And "PHP T-Shirt" variant should not be discounted

    @api @ui
    Scenario: Removing a scheduled catalog promotion
        Given it is "2022-08-22" now
        And the catalog promotion "Winter Sale" operates between "2022-12-01" and "2023-02-28"
        When I request the removal of "Winter Sale" catalog promotion
        Then I should be notified that the removal operation has started successfully
        And there should be an empty list of catalog promotions
        And "PHP T-Shirt" variant should not be discounted
