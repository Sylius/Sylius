@managing_customer_groups
Feature: Filtering customer groups
    In order to see specific customer groups in the store
    As an Administrator
    I want to filter customer groups

    Background:
        Given the store has a customer group "Online sale" with "sale" code
        And the store has a customer group "Retail" with "retail" code
        And the store has a customer group "Wholesale" with "whole" code
        And I am logged in as an administrator

    @api-todo @ui
    Scenario: Filtering customer groups
        When I browse customer groups
        And I search for them by "sale"
        Then there should be 2 customer groups in the list
        And the 1st customer group on the list should have code "sale" and name "Online sale"
        And the 2nd customer group on the list should have code "whole" and name "Wholesale"
