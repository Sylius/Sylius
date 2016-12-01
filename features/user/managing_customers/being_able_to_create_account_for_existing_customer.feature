@managing_customers
Feature: Create account option availability
    In order to correctly administrate customers
    As an Administrator
    I want not see create account option if the customer account already exists

    Background:
        And I am logged in as an administrator

    @ui @javascript
    Scenario: Being able to create an account for created customer
        Given I want to create a new customer
        And I do not choose create account option
        And I specify their email as "bananaPotato@example.com"
        And I add them
        Then I should be notified that it has been successfully created
        And I should not be able to specify their password
        And I should be able to select create account option

    @ui @javascript
    Scenario: Not seeing create account option after adding customer with account
        Given I want to create a new customer account
        And I choose create account option
        And I specify their password as "Banana"
        And I specify their email as "bananaPotato@example.com"
        And I add them
        Then I should be notified that it has been successfully created
        And I should be able to specify their password
        And I should not see create account option
