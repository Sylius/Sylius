@managing_customer_groups
Feature: Customer group unique code validation
    In order to avoid making mistakes when managing customer groups
    As an Administrator
    I want to be prevented from adding a new customer group with an existing code

    Background:
        Given the store has a customer group "Retail" with "RETAIL" code
        And I am logged in as an administrator

    @ui
    Scenario: Trying to add a new customer group with used code
        Given I want to create a new customer group
        When I specify its code as "RETAIL"
        And I add it
        Then I should be notified that customer group with this code already exists
        And I should see 1 customer groups in the list
        And I should see the customer group "Retail" in the list
