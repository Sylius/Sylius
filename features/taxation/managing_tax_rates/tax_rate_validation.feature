@managing_tax_rates
Feature: Tax rate validation
    In order to avoid making mistakes when managing a tax rate
    As an Administrator
    I want to be prevented from adding it without specifying required fields

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a tax category "Food and Beverage"
        And I am logged in as an administrator

    @ui
    Scenario: Trying to add a new tax rate without specifying its code
        Given I want to create a new tax rate
        When I name it "Food and Beverage Tax Rates"
        But I do not specify its code
        And I try to add it
        Then I should be notified that code is required
        And tax rate with name "Food and Beverage Tax Rates" should not be added

    @ui
    Scenario: Trying to add a new tax rate without specifying its amount
        Given I want to create a new tax rate
        When I name it "Food and Beverage Tax Rates"
        But I do not specify its amount
        And I try to add it
        Then I should be notified that amount is required
        And tax rate with name "Food and Beverage Tax Rates" should not be added

    @ui
    Scenario: Trying to add a new tax rate without specifying its name
        Given I want to create a new tax rate
        When I specify its code as "UNITED_STATES_SALES_TAX"
        But I do not name it
        And I try to add it
        Then I should be notified that name is required
        And tax rate with code "UNITED_STATES_SALES_TAX" should not be added

    @ui
    Scenario: Trying to add a new tax rate without specifying its zone
        Given the store does not have any zones defined
        And I want to create a new tax rate
        When I name it "Food and Beverage Tax Rates"
        But I do not specify its zone
        And I try to add it
        Then I should be notified that zone has to be selected
        And tax rate with name "Food and Beverage Tax Rates" should not be added

    @ui
    Scenario: Trying to add a new tax rate without specifying its category
        Given the store does not have any categories defined
        And I want to create a new tax rate
        When I name it "Food and Beverage Tax Rates"
        But I do not specify related tax category
        And I try to add it
        Then I should be notified that category has to be selected
        And tax rate with name "Food and Beverage Tax Rates" should not be added

    @ui
    Scenario: Trying to remove amount from existing tax rate
        Given the store has "United States Sales Tax" tax rate of 20% for "Sports gear" within the "US" zone
        And I want to modify this tax rate
        When I remove its amount
        And I try to save my changes
        Then I should be notified that amount is required
        And this tax rate amount should still be 20%

    @ui
    Scenario: Trying to remove name from existing tax rate
        Given the store has "United States Sales Tax" tax rate of 20% for "Sports gear" within the "US" zone
        And I want to modify this tax rate
        When I remove its name
        And I try to save my changes
        Then I should be notified that name is required
        And this tax rate should still be named "United States Sales Tax"

    @ui
    Scenario: Trying to remove zone from existing tax rate
        Given the store has "United States Sales Tax" tax rate of 20% for "Sports gear" within the "US" zone
        And I want to modify this tax rate
        When I remove its zone
        And I try to save my changes
        Then I should be notified that zone has to be selected
