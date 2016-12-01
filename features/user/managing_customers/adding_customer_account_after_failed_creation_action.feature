@managing_customers
Feature: Adding a new customer account after failed creation action
    In order to be able to create customer with account even if the creation failed
    As an Administrator
    I want to be able to add a customer with an user account after failed creation action

    Background:
        Given I am logged in as an administrator

    @ui @javascript
    Scenario: Trying to add new customer with an account without required information
        Given I want to create a new customer account
        When I choose create account option
        And I do not specify any information
        And I try to add them
        Then I should still be on the customer creation page
        And I should be able to specify their password
        And I should not be able to select create account option
        And I should be notified that email is required
        And I should be notified that password is required

    @ui @javascript
    Scenario: Trying to add new customer with an account without email
        Given I want to create a new customer account
        When I choose create account option
        And I specify their password as "Banana"
        But I do not specify their email
        And I try to add them
        Then I should still be on the customer creation page
        And I should be able to specify their password
        And I should not be able to select create account option
        And I should be notified that email is required

    @ui @javascript
    Scenario: Trying to add new customer without an account without email
        Given I want to create a new customer account
        When I do not choose create account option
        And I do not specify their email
        And I try to add them
        Then I should still be on the customer creation page
        And I should not be able to specify their password
        And I should be able to select create account option
        And I should be notified that email is required

    @ui @javascript
    Scenario: Trying to add new customer with an account without required information
        Given I want to create a new customer account
        When I choose create account option
        And I specify their password as "Na"
        And I try to add them
        Then I should still be on the customer creation page
        And I should be able to specify their password
        And I should not be able to select create account option
        And I should be notified that the password must be at least 4 characters long
        And I should be notified that email is required
