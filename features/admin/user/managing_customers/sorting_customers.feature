@managing_customers
Feature: Filtering customers by firstname, lastname and email
    In order to quickly find customers
    As an Administrator
    I want to search for a specific customer

    Background:
        Given the store has customer "f.baggins@shire.me" with name "Frodo Baggins" since "2011-01-10 21:00"
        And the store has customer "l.abhorsen@example.com" with name "Lirael Abhorsen" since "2020-01-01 10:00"
        And the store has customer "g.bespoke@example.com" with name "Ghastly Bespoke" since "2000-10-11 15:00"
        And I am logged in as an administrator

    @ui @api-todo
    Scenario: Customers are sorted by descending registration date
        When I want to see all customers in store
        Then I should see 3 customers on the list
        And the first customer should be "l.abhorsen@example.com"
        And the last customer should be "g.bespoke@example.com"

    @ui @api-todo
    Scenario: Sorting customers by ascending registration date
        When I want to see all customers in store
        And I sort customers by ascending registration date
        Then I should see 3 customers on the list
        And the first customer should be "g.bespoke@example.com"
        And the last customer should be "l.abhorsen@example.com"

    @ui @api-todo
    Scenario: Sorting customers by descending email
        When I want to see all customers in store
        And I sort customers by descending email
        Then I should see 3 customers on the list
        And the first customer should be "l.abhorsen@example.com"
        And the last customer should be "f.baggins@shire.me"

    @ui @api-todo
    Scenario: Sorting customers by ascending email
        When I want to see all customers in store
        And I sort customers by ascending email
        Then I should see 3 customers on the list
        And the first customer should be "f.baggins@shire.me"
        And the last customer should be "l.abhorsen@example.com"

    @ui @api-todo
    Scenario: Sorting customers by descending first name
        When I want to see all customers in store
        And I sort customers by descending first name
        Then I should see 3 customers on the list
        And the first customer should be "l.abhorsen@example.com"
        And the last customer should be "f.baggins@shire.me"

    @ui @api-todo
    Scenario: Sorting customers by ascending first name
        When I want to see all customers in store
        And I sort customers by ascending first name
        Then I should see 3 customers on the list
        And the first customer should be "f.baggins@shire.me"
        And the last customer should be "l.abhorsen@example.com"

    @ui @api-todo
    Scenario: Sorting customers by descending last name
        When I want to see all customers in store
        And I sort customers by descending last name
        Then I should see 3 customers on the list
        And the first customer should be "g.bespoke@example.com"
        And the last customer should be "l.abhorsen@example.com"

    @ui @api-todo
    Scenario: Sorting customers by ascending last name
        When I want to see all customers in store
        And I sort customers by ascending last name
        Then I should see 3 customers on the list
        And the first customer should be "l.abhorsen@example.com"
        And the last customer should be "g.bespoke@example.com"
