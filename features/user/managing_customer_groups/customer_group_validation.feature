@managing_customer_groups
Feature: Customer group validation
    In order to avoid making mistakes when managing customer groups
    As an Administrator
    I want to be prevented from adding it without specifying required fields

    Background:
        Given I am logged in as an administrator

    @ui
    Scenario: Trying to add a new customer group without a name
        Given I want to create a new customer group
        When I try to add it
        Then I should be notified that name is required

    @ui
    Scenario: Trying to remove name from an existing customer group
        Given the store has a customer group "Retail"
        And I want to edit this customer group
        When I remove its name
        And I try to save my changes
        Then I should be notified that name is required
        And this customer group should still be named "Retail"
