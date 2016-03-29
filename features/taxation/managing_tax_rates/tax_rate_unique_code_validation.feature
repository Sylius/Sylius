@managing_tax_rates
Feature: Tax rate unique code validation
    In order to uniquely identify tax rates
    As an Administrator
    I want to be prevented from adding two tax rates with same code

    Background:
        Given there is a zone "EU" containing all members of the European Union
        And the store has a tax category "Sports gear"
        And the store has "European Union Sales Tax" tax rate of 20% for "Sports gear" within "EU" zone identified by "EUROPEAN_UNION_SALES_TAX" code
        And I am logged in as an administrator

    @ui
    Scenario: Trying to add tax rate with taken code
        Given I want to create a new tax rate
        When I specify its code as "EUROPEAN_UNION_SALES_TAX"
        And I name it "European Union Sales Tax"
        And I define it for the "European Union" zone
        And I make it applicable for the "Sports gear" tax category
        And I specify its amount as 20%
        And I choose the default tax calculator
        And I try to add it
        Then I should be notified that tax rate with this code already exists
        And there should still be only one tax rate with code "EUROPEAN_UNION_SALES_TAX"
