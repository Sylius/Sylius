@managing_taxons
Feature: Taxon validation
    In order to avoid making mistakes when managing a taxons
    As an Administrator
    I want to be prevented from adding it without specifying required fields

    Background:
        Given the store is available in "English (United States)"
        And I am logged in as an administrator

    @ui
    Scenario: Trying to add a taxon without specifying its code
        Given I want to create a new taxon
        When I do not specify its code
        And I name it "T-Shirts" in "English (United States)"
        And I try to add it
        Then I should be notified that code is required
        And Taxon named "T-Shirts" should not be added

    @ui
    Scenario: Trying to add a taxon without specifying its name
        Given I want to create a new taxon
        When I specify its code as "t-shirts"
        And I do not specify its name
        And I try to add it
        Then I should be notified that name is required
