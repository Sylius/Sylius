@managing_customers
Feature: Browsing customers
    In order to see all customers in the store
    As an Administrator
    I want to browse customers

    Background:
        Given the store has customer "f.baggins@example.com"
        And the store has customer "mr.banana@example.com"
        And the store has customer "l.skywalker@example.com"
        And I am logged in as an administrator

    @ui
    Scenario: Browsing customers in store
        When I want to see all customers in store
        Then I should see 3 customers in the list
        And I should see the customer "mr.banana@example.com" in the list
