@managing_tax_categories
Feature: Tax category unique code validation
    In order to uniquely identify tax categories
    As an Administrator
    I want to be prevented from adding two tax categories with same code

    Background:
        Given the store has a tax category "Alcohol" with a code "alcohol"
        And I am logged in as an administrator

    @ui
    Scenario: Trying to add tax category with taken code
        Given I want to create a new tax category
        When I name it "Food and Beverage"
        And I specify its code as "alcohol"
        And I try to add it
        Then I should be notified that tax category with this code already exists
        And there should still be only one tax category with code "alcohol"
