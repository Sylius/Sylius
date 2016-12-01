@managing_customers
Feature: Customer validation
    In order to avoid making mistakes when managing customers
    As an Administrator
    I want to be prevented from adding it without specifying required fields

    Background:
        Given I am logged in as an administrator

    @ui
    Scenario: Trying to add a new customer without an email
        Given I want to create a new customer
        When I specify their first name as "Luke"
        And I specify their last name as "Skywalker"
        And I try to add them
        Then I should be notified that email is required

    @ui
    Scenario: Trying to specify too short first name for an existing customer
        Given the store has customer "l.skywalker@gmail.com"
        And I want to edit this customer
        When I specify their first name as "L"
        And I try to save my changes
        Then I should be notified that first name should be at least 2 characters long
        And the customer "l.skywalker@gmail.com" should still have an empty first name

    @ui
    Scenario: Trying to specify too short last name for an existing customer
        Given the store has customer "l.skywalker@gmail.com" with first name "Luke"
        And I want to edit this customer
        When I specify their last name as "S"
        And I try to save my changes
        Then I should be notified that last name should be at least 2 characters long
        And the customer "l.skywalker@gmail.com" should still have an empty last name

    @ui
    Scenario: Trying to remove email from an existing customer
        Given the store has customer "l.skywalker@gmail.com"
        And I want to edit this customer
        When I remove its email
        And I try to save my changes
        Then I should be notified that email is required
        And the customer "l.skywalker@gmail.com" should still have this email

    @ui
    Scenario: Trying to create customer with wrong email format
        Given I want to create a new customer
        When I specify their email as "wrongemail"
        And I try to add them
        Then I should be notified that email is not valid
        And the customer with email "wrongemail" should not appear in the store
