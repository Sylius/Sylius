@managing_catalog_promotions
Feature: Managing catalog promotions
    In order to set up a catalog promotion for some special occasions
    As an Administrator
    I want to have an option to configure such a promotion

    Background:
        Given the store operates on a single channel in "United States"
        And I am logged in as an administrator

    @api
    Scenario: Creating simple catalog promotion only with code and name
        When I create a new catalog promotion with "winter_sale" code and "Winter sale" name
        Then there should be 1 new catalog promotion on the list
        And it should have "winter_sale" code and "Winter sale" name
        And this catalog promotion should be usable
