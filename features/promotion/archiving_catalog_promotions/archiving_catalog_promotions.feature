@managing_catalog_promotions
Feature: Archiving catalog promotions
    In order to be in control of possible catalog promotions that exist on the system
    As an Administrator
    I want to have an option to archive such a promotion

    Background:
        Given the store operates on a single channel in "United States"
        And the store has "Clothes" taxonomy
        And the store has a "T-Shirt" configurable product
        And this product belongs to "Clothes"
        And this product has "PHP T-Shirt" variant priced at "$20.00"
        And there is a catalog promotion "Winter Sale" that reduces price by "30%" and applies on "Clothes" taxon
        And the store has "Cups" taxonomy
        And the store has a "Cup" configurable product
        And this product belongs to "Cups"
        And this product has "PHP Cup" variant priced at "$10.00"
        And there is a catalog promotion "Spring Sale" that reduces price by "10%" and applies on "Cups" taxon
        And I am logged in as an administrator

    @api @ui
    Scenario: Archiving an expired catalog promotion
        Given it is "2022-08-22" now
        And the catalog promotion "Winter Sale" operates between "2021-12-20" and "2021-12-30"
        When I request the archivation of the "Winter Sale" catalog promotion
        Then I should be notified that the archival operation has started successfully
        And there should be 1 catalog promotions on the list
        And "PHP T-Shirt" variant should not be discounted

    @api @ui
    Scenario: Archiving an active catalog promotion without any time limits
        When I request the archivation of the "Winter Sale" catalog promotion
        Then I should be notified that the archival operation has started successfully
        And there should be 1 catalog promotions on the list
        And "PHP T-Shirt" variant should not be discounted

    @api @ui
    Scenario: Archiving an active catalog promotion in the time period
        Given it is "2022-12-15" now
        And the catalog promotion "Winter Sale" operates between "2022-12-01" and "2022-12-30"
        When I request the archivation of the "Winter Sale" catalog promotion
        Then I should be notified that the archival operation has started successfully
        And there should be 1 catalog promotions on the list
        And "PHP T-Shirt" variant should not be discounted

    @api @ui
    Scenario: Archiving a scheduled catalog promotion
        Given it is "2022-08-22" now
        And the catalog promotion "Winter Sale" operates between "2022-12-01" and "2023-02-28"
        When I request the archivation of the "Winter Sale" catalog promotion
        Then I should be notified that the archival operation has started successfully
        And there should be 1 catalog promotions on the list
        And "PHP T-Shirt" variant should not be discounted

    @domain
    Scenario: Archiving a catalog promotion does not remove it from the database
        When I archive the "Winter Sale" catalog promotion
        Then the catalog promotion should still exist in the registry

    @api @ui
    Scenario: Seeing only archived catalog promotions
        Given the "Winter Sale" catalog promotion is archival
        When I browse catalog promotions
        And I filter archival catalog promotions
        Then I should see a single catalog promotion in the list
        And the "Winter Sale" catalog promotion should be listed on the current page
        And the "Spring Sale" catalog promotion shouldn't be listed on the current page

    @api @ui
    Scenario: Restoring an archival catalog promotion
        Given the "Winter Sale" catalog promotion is archival
        When I browse catalog promotions
        And I filter archival catalog promotions
        And I restore the "Winter Sale" catalog promotion
        And I should be notified that the catalog promotion has been successfully restored
        And I browse catalog promotions once again
        Then I should be viewing non archival catalog promotions
        And I should see 2 catalog promotions on the list
        And the "Winter Sale" catalog promotion should be listed on the current page
        And the "Spring Sale" catalog promotion should be listed on the current page
