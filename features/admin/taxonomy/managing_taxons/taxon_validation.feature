@managing_taxons
Feature: Taxon validation
    In order to avoid making mistakes when managing taxons
    As an Administrator
    I want to be prevented from adding any without specifying required fields

    Background:
        Given the store is available in "English (United States)"
        And I am logged in as an administrator

    @no-ui @api
    Scenario: Trying to add taxon translation in unexisting locale
        Given the store classifies its products as "Jeans"
        When I want to modify the "Jeans" taxon
        And I name it "Jeans" in "French (France)"
        And I save my changes
        Then I should be notified that the locale is not available

    @ui @api
    Scenario: Trying to add a taxon without specifying its code
        When I want to create a new taxon
        And I do not specify its code
        And I name it "T-Shirts" in "English (United States)"
        And I try to add it
        Then I should be notified that code is required
        And taxon named "T-Shirts" should not be added

    @ui @api
    Scenario: Trying to add a taxon without specifying its name
        When I want to create a new taxon
        And I specify its code as "t-shirts"
        And I do not specify its name
        And I try to add it
        Then I should be notified that name is required

    @ui @api
    Scenario: Trying to add a taxon with a too long code
        When I want to create a new taxon
        And I name it "T-Shirts" in "English (United States)"
        And I specify a too long code
        And I try to add it
        Then I should be notified that code is too long

    @ui @no-api
    Scenario: Trying to add a taxon without specifying its slug
        When I want to create a new taxon
        And I specify its code as "t-shirts"
        And I name it "T-Shirts" in "English (United States)"
        And I do not specify its slug
        And I try to add it
        Then I should be notified that slug is required

    @ui @api
    Scenario: Trying to add a taxon with non unique slug
        Given the store classifies its products as "T-Shirts"
        When I want to create a new taxon
        And I specify its code as "t-shirts-2"
        And I name it "T-Shirts" in "English (United States)"
        And I set its slug to "t-shirts" in "English (United States)"
        And I try to add it
        Then I should be notified that taxon slug must be unique
