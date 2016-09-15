@managing_tax_rates
Feature: Editing tax rate
    In order to change taxes applied to products
    As an Administrator
    I want to be able to edit a tax rate

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a tax category "Sports gear"
        And the store has "United States Sales Tax" tax rate of 20% for "Sports gear" within the "US" zone
        And I am logged in as an administrator

    @todo
    Scenario: Trying to change tax rate code
        Given I want to modify a tax rate "United States Sales Tax"
        When I change its code to "us_vat"
        And I save my changes
        Then I should be notified that code cannot be changed
        And tax rate "United States Sales Tax" should still have code "united_states_sales_tax"

    @ui
    Scenario: Seeing disabled code field when editing tax rate
        When I want to modify a tax rate "United States Sales Tax"
        Then the code field should be disabled

    @ui
    Scenario: Renaming the tax rate
        Given I want to modify a tax rate "United States Sales Tax"
        When I rename it to "US VAT"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this tax rate name should be "US VAT"

    @ui
    Scenario: Changing the tax rate amount
        Given I want to modify a tax rate "United States Sales Tax"
        When I specify its amount as 16%
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this tax rate amount should be 16%

    @ui
    Scenario: Changing related tax category
        Given the store has a tax category "Food and Beverage" also
        And I want to modify a tax rate "United States Sales Tax"
        When I change it to be applicable for the "Food and Beverage" tax category
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this tax rate should be applicable for the "Food and Beverage" tax category

    @ui
    Scenario: Changing related zone
        Given there is a zone "The Rest of the World" containing all other countries
        And I want to modify a tax rate "United States Sales Tax"
        When I change its zone to "The Rest of the World"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this tax rate should be applicable in "The Rest of the World" zone
