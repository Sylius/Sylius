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

    @ui @api
    Scenario: Inability of changing the code of an existing tax rate
        When I want to modify a tax rate "United States Sales Tax"
        Then I should not be able to edit its code

    @ui @api
    Scenario: Renaming the tax rate
        When I want to modify a tax rate "United States Sales Tax"
        And I rename it to "US VAT"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this tax rate name should be "US VAT"

    @ui @api
    Scenario: Changing the tax rate amount
        When I want to modify a tax rate "United States Sales Tax"
        And I specify its amount as 16%
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this tax rate amount should be 16%

    @ui @api
    Scenario: Changing related tax category
        Given the store has a tax category "Food and Beverage" also
        When I want to modify a tax rate "United States Sales Tax"
        And I change it to be applicable for the "Food and Beverage" tax category
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this tax rate should be applicable for the "Food and Beverage" tax category

    @ui @api
    Scenario: Changing related zone
        Given there is a zone "The Rest of the World" containing all other countries
        When I want to modify a tax rate "United States Sales Tax"
        And I change its zone to "The Rest of the World"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this tax rate should be applicable in "The Rest of the World" zone
