@managing_customer_groups
Feature: Sorting customer groups
    In order to see all customer groups in the store sorted in a specific way
    As an Administrator
    I want to sort customer groups

    Background:
        Given the store has a customer group "Wholesale" with "aaa" code
        And the store has a customer group "Retail" with "bbb" code
        And the store has a customer group "Online sale" with "ccc" code
        And I am logged in as an administrator

    @api-todo @ui
    Scenario: Sorting customer groups by name in ascending order
        When I browse customer groups
        And I sort them by the name in ascending order
        Then there should be 3 customer groups in the list
        And the 1st customer group on the list should have code "ccc" and name "Online sale"
        And the 2nd customer group on the list should have code "bbb" and name "Retail"
        And the 3rd customer group on the list should have code "aaa" and name "Wholesale"

    @api-todo @ui
    Scenario: Sorting customer groups by name in descending order
        When I browse customer groups
        And I sort them by the name in descending order
        Then there should be 3 customer groups in the list
        And the 1st customer group on the list should have code "aaa" and name "Wholesale"
        And the 2nd customer group on the list should have code "bbb" and name "Retail"
        And the 3rd customer group on the list should have code "ccc" and name "Online sale"

    @api-todo @ui
    Scenario: Sorting customer groups by code in ascending order
        When I browse customer groups
        And I sort them by the code in ascending order
        Then there should be 3 customer groups in the list
        And the 1st customer group on the list should have code "aaa" and name "Wholesale"
        And the 2nd customer group on the list should have code "bbb" and name "Retail"
        And the 3rd customer group on the list should have code "ccc" and name "Online sale"

    @api-todo @ui
    Scenario: Sorting customer groups by code in descending order
        When I browse customer groups
        And I sort them by the code in descending order
        Then there should be 3 customer groups in the list
        And the 1st customer group on the list should have code "ccc" and name "Online sale"
        And the 2nd customer group on the list should have code "bbb" and name "Retail"
        And the 3rd customer group on the list should have code "aaa" and name "Wholesale"
