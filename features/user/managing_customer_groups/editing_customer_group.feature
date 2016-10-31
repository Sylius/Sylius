@managing_customer_groups
Feature: Editing a customer group
    In order to change information about a customer group
    As an Administrator
    I want to be able to edit the customer group

    Background:
        Given the store has a customer group "Retail"
        And I am logged in as an administrator

    @ui
    Scenario: Changing name of an existing customer group
        When I want to edit this customer group
        And I specify its name as "Wholesale"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this customer group with name "Wholesale" should appear in the store

    @ui
    Scenario: Seeing disabled code field while editing customer group
        When I want to edit this customer group
        Then the code field should be disabled
