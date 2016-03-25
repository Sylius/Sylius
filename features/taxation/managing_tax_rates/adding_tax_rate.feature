@managing_tax_rates
Feature: Adding a new tax rate
    In order to apply different tax rates to various tax categories
    As an Administrator
    I want to add a new tax rate to the registry

    Background:
        Given there is a zone "EU" containing all members of the European Union
        And the store has a tax category "Food and Beverage"
        And I am logged in as an administrator

    @ui
    Scenario: Adding a new tax rate
        Given I want to create a new tax rate
        When I specify its code as "EUROPEAN_UNION_SALES_TAX"
        And I name it "European Union Sales Tax"
        And I define it for the "European Union" zone
        And I make it applicable for the "Food and Beverage" tax category
        And I specify its amount as 20%
        And I choose the default tax calculator
        And I add it
        Then I should be notified that it has been successfully created
        And the tax rate "European Union Sales Tax" should appear in the registry
