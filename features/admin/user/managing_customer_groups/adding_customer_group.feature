@managing_customer_groups
Feature: Adding a new customer group
    In order to categorize my customers
    As an Administrator
    I want to add a new customer group to the store

    Background:
        Given I am logged in as an administrator

    @ui @api
    Scenario: Adding a new customer group
        When I want to create a new customer group
        And I specify its code as "RETAIL"
        And I specify its name as "Retail"
        And I add it
        Then I should be notified that it has been successfully created
        And the customer group "Retail" should appear in the store
