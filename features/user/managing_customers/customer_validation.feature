@managing_customers
Feature: Customer validation
    In order to avoid making mistakes when managing customers
    As an Administrator
    I want to be prevented from adding it without specify required fields

    Background:
        Given I am logged in as an administrator

    @ui
    Scenario: Trying to add a new customer without a first name
        Given I want to create a new customer
        When I specify their last name as "Skywalker"
        And I specify their email as "l.skywalker@gmail.com"
        And I try to add it
        Then I should be notified that first name is required
        And the customer with email "l.skywalker@gmail.com" should not appear in the store

    @ui
    Scenario: Trying to add a new customer without a last name
        Given I want to create a new customer
        When I specify their first name as "Luke"
        And I specify their email as "l.skywalker@gmail.com"
        And I try to add it
        Then I should be notified that last name is required
        And the customer with email "l.skywalker@gmail.com" should not appear in the store

    @ui
    Scenario: Trying to add a new customer without an email
        Given I want to create a new customer
        When I specify their first name as "Luke"
        And I specify their last name as "Skywalker"
        And I try to add it
        Then I should be notified that email is required
        And the customer with email "l.skywalker@gmail.com" should not appear in the store

    @ui
    Scenario: Trying to remove first name from existing customer
        Given the store has customer "l.skywalker@gmail.com" with first name "Luke"
        Given I want to edit the customer "l.skywalker@gmail.com"
        When I remove its first name
        And I try to save my changes
        Then I should be notified that first name is required
        And the customer "l.skywalker@gmail.com" should still have first name "Luke"

    @ui
    Scenario: Trying to remove last name from existing customer
        Given the store has customer "l.skywalker@gmail.com" with last name "Skywalker"
        Given I want to edit the customer "l.skywalker@gmail.com"
        When I remove its last name
        And I try to save my changes
        Then I should be notified that last name is required
        And the customer "l.skywalker@gmail.com" should still have last name "Skywalker"

    @ui
    Scenario: Trying to remove email from existing customer
        Given the store has customer "l.skywalker@gmail.com"
        Given I want to edit the customer "l.skywalker@gmail.com"
        When I remove its email
        And I try to save my changes
        Then I should be notified that email is required
        And the customer "l.skywalker@gmail.com" should still have this email

    @ui
    Scenario: Trying to create customer with wrong email format
        Given I want to create a new customer
        When I specify their first name as "Luke"
        And I specify their last name as "Skywalker"
        And I specify their email as "wrongemail"
        And I try to add it
        Then I should be notified that email is not valid
        And the customer with email "wrongemail" should not appear in the store
