@managing_customer_groups
Feature: Customer Group validation
    In order to avoid making mistakes when managing customer groups
    As an Administrator
    I want to be prevented from adding or editing it with invalid data

    Background:
        Given I am logged in as an administrator

    @ui @api
    Scenario: Adding a new customer group with too long code
        Given I want to create a new customer group
        And I specify its name as "Retail"
        When I specify its code as 256 characters long string
        And I add it
        Then I should be notified that the code is too long
