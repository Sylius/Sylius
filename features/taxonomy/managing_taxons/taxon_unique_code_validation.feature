@managing_taxons
Feature: Taxon unique code validation
    In order to uniquely identify taxons
    As an Administrator
    I want to be prevented from adding two taxons with same code

    Background:
        Given the store is available in "English (United States)"
        And the store classifies its products as "T-Shirts"
        And I am logged in as an administrator

    @ui
    Scenario: Trying to add taxon with taken code
        Given I want to create a new taxon
        When I specify its code as "t_shirts"
        And I name it "T-Shirts" in "English (United States)"
        And I try to add it
        Then I should be notified that taxon with this code already exists
        And there should still be only one taxon with code "t_shirts"
