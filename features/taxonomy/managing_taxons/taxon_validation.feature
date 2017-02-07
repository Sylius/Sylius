@managing_taxons
Feature: Taxon validation
    In order to avoid making mistakes when managing taxons
    As an Administrator
    I want to be prevented from adding any without specifying required fields

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
        And taxon named "T-Shirts" should not be added

    @ui
    Scenario: Trying to add a taxon without specifying its name
        Given I want to create a new taxon
        When I specify its code as "t-shirts"
        And I do not specify its name
        And I try to add it
        Then I should be notified that name is required

    @ui
    Scenario: Trying to add a taxon without specifying its slug
        Given I want to create a new taxon
        When I specify its code as "t-shirts"
        And I name it "T-Shirts" in "English (United States)"
        And I do not specify its slug
        And I try to add it
        Then I should be notified that slug is required

    @ui
    Scenario: Trying to add a taxon with non unique slug
        Given the store classifies its products as "T-Shirts"
        When I want to create a new taxon
        And I specify its code as "t-shirts-2"
        And I name it "T-Shirts" in "English (United States)"
        And I set its slug to "t-shirts"
        And I try to add it
        Then I should be notified that taxon slug must be unique
