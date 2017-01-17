@managing_customers
Feature: Adding a new customer account
    In order to allow my customers to sign in
    As an Administrator
    I want to add a customer with an user account

    Background:
        Given I am logged in as an administrator

    @ui @javascript
    Scenario: Adding a new customer with an account
        Given I want to create a new customer account
        When I specify their email as "l.skywalker@gmail.com"
        And I choose create account option
        And I specify their password as "psw123"
        And I add them
        Then I should be notified that it has been successfully created
        And the customer "l.skywalker@gmail.com" should appear in the store
        And the customer "l.skywalker@gmail.com" should have an account created

    @ui @javascript
    Scenario: Creating an account for existing customer
        Given the store has customer "Frodo Baggins" with email "f.baggins@example.com"
        And I want to edit this customer
        When I choose create account option
        And I specify their password as "killSauron"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And the customer "f.baggins@example.com" should appear in the store
        And this customer should have an account created
