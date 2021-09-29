@managing_catalog_promotions
Feature: Browsing catalog promotions
    In order to have an overview of all defined catalog promotions
    As an Administrator
    I want to be able to browse list of them

    Background:
        Given the store operates on a single channel in "United States"
        And there are catalog promotions named "Summer sale" and "Winter sale"
        And I am logged in as an administrator

    @api @ui @todo
    Scenario: Browsing defined catalog promotions
        When I browse catalog promotions
        Then there should be 2 catalog promotions on the list
        And the catalog promotions named "Summer sale" and "Winter sale" should be in the registry
