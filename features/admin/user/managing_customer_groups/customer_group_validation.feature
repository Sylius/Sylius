@managing_customer_groups
Feature: Customer group validation
    In order to avoid making mistakes when managing customer groups
    As an Administrator
    I want to be prevented from adding it without specifying required fields

    Background:
        Given I am logged in as an administrator

    @todo @ui @api
    Scenario: Trying to add a new customer group without a name
        When I want to create a new customer group
        And I try to add it
        Then I should be notified that name is required
        And I should be informed that this form contains errors

    @todo @ui @api
    Scenario: Trying to remove name from an existing customer group
        Given the store has a customer group "Retail"
        When I want to edit this customer group
        And I remove its name
        And I try to save my changes
        Then I should be notified that name is required
        And I should be informed that this form contains errors
        And this customer group should still be named "Retail"
