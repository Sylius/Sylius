@managing_customers
Feature: Customer validation
    In order to avoid making mistakes when managing customers
    As an Administrator
    I want to be prevented from adding it without specifying required fields

    Background:
        Given I am logged in as an administrator

    @api @ui
    Scenario: Trying to add a new customer without an email
        When I want to create a new customer
        And I specify their first name as "Luke"
        And I specify their last name as "Skywalker"
        And I try to add them
        Then I should be notified that email is required

    @api @ui
    Scenario: Trying to specify too short first name for an existing customer
        Given the store has customer "l.skywalker@gmail.com"
        When I want to edit this customer
        And I specify their first name as "L"
        And I try to save my changes
        Then I should be notified that first name should be at least 2 characters long
        And the customer "l.skywalker@gmail.com" should still have an empty first name

    @api @ui
    Scenario: Trying to specify too short last name for an existing customer
        Given the store has customer "l.skywalker@gmail.com" with first name "Luke"
        When I want to edit this customer
        And I specify their last name as "S"
        And I try to save my changes
        Then I should be notified that last name should be at least 2 characters long
        And the customer "l.skywalker@gmail.com" should still have an empty last name

    @api @ui
    Scenario: Trying to remove email from an existing customer
        Given the store has customer "l.skywalker@gmail.com"
        When I want to edit this customer
        And I remove its email
        And I try to save my changes
        Then I should be notified that email is required
        And the customer "l.skywalker@gmail.com" should still have this email

    @api @ui
    Scenario: Trying to create customer with wrong email format
        When I want to create a new customer
        And I specify their email as "wrongemail"
        And I try to add them
        Then I should be notified that email is not valid
        And the customer with email "wrongemail" should not appear in the store

    @api @ui
    Scenario: Trying to create customer with wrong email format in strict mode
        When I want to create a new customer
        And I specify their email as "wrongemail@example..com"
        And I try to add them
        Then I should be notified that email is not valid
        And the customer with email "wrongemail@example..com" should not appear in the store
