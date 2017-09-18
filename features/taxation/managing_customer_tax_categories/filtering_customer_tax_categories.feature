@managing_customer_tax_categories
Feature: Filtering customer tax categories
    In order to quickly find customer tax categories by specific data
    As an Administrator
    I want to be able to filter customer tax categories

    Background:
        Given the store has a customer tax category "Retail" with a code "retail"
        And the store has a customer tax category "Wholesale" with a code "wholesale"
        And I am logged in as an administrator

    @ui @todo
    Scenario: Filtering customer tax categories by the name
        Given I am browsing customer tax categories
        When I filter customer tax categories with value containing "Ret"
        Then I should see a single customer tax category in the list
        And I should see the customer tax category "Retail" in the list

    @ui @todo
    Scenario: Filtering customer tax categories by the code
        Given I am browsing customer tax categories
        When I filter customer tax categories with value containing "whole"
        Then I should see a single customer tax category in the list
        And I should see the customer tax category "Wholesale" in the list
