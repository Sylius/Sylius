@managing_tax_rates
Feature: Tax rate unique code validation
    In order to uniquely identify tax rates
    As an Administrator
    I want to be prevented from adding two tax rates with same code

    Background:
        Given the store operates on a single channel in "United States"
        And the store has a tax category "Sports gear"
        And the store has "United States Sales Tax" tax rate of 20% for "Sports gear" within the "US" zone identified by the "UNITED_STATES_SALES_TAX" code
        And I am logged in as an administrator

    @ui
    Scenario: Trying to add tax rate with taken code
        Given I want to create a new tax rate
        When I specify its code as "UNITED_STATES_SALES_TAX"
        And I name it "United States Sales Tax"
        And I define it for the "United States" zone
        And I make it applicable for the "Sports gear" tax category
        And I specify its amount as 20%
        And I choose the default tax calculator
        And I try to add it
        Then I should be notified that tax rate with this code already exists
        And there should still be only one tax rate with code "UNITED_STATES_SALES_TAX"
