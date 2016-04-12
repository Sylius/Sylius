@managing_customers
Feature: Adding a new customer account
    In order to allows customers create accounts
    As an Administrator
    I want to add a customer account to the registry

    Background:
        Given I am logged in as an administrator

    @ui @javascript
    Scenario: Adding a new customer with an account
        Given I want to create a new customer account
        When I specify its first name as "Luke"
        And I specify its last name as "Skywalker"
        And I specify its email as "l.skywalker@gmail.com"
        And I choose create account option
        And I specify its password as "psw123"
        And I add it
        Then I should be notified that it has been successfully created
        And the customer account "l.skywalker@gmail.com" with password should appear in the registry

    @ui @javascript
    Scenario: Creating an account for existing customer
        Given the store has customer "Frodo Baggins" with email "f.baggins@example.com"
        And I want to edit the customer "f.baggins@example.com"
        When I choose create account option
        And I specify its password as "killSauron"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And the customer account "f.baggins@example.com" with password should appear in the registry
