@managing_customer_tax_categories
Feature: Customer tax category unique code validation
    In order to uniquely identify customer tax categories
    As an Administrator
    I want to be prevented from adding two customer tax categories with the same code

    Background:
        Given the store has a customer tax category "Retail" with a code "retail"
        And I am logged in as an administrator

    @ui
    Scenario: Trying to add a customer tax category with a taken code
        When I want to create a new customer tax category
        And I name it "Retail"
        And I specify its code as "retail"
        And I try to add it
        Then I should be notified that a customer tax category with this code already exists
        And there should still be only one customer tax category with a code "retail"
