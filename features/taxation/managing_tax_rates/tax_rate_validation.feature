@managing_tax_rates
Feature: Tax rate validation
    In order to avoid making mistakes when managing a tax rate
    As an Administrator
    I want to be prevented from adding it without specifying required fields

    Background:
        Given there is a zone "EU" containing all members of the European Union
        And the store has a tax category "Food and Beverage"
        Given I am logged in as an administrator

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
