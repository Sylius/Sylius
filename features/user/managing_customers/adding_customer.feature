@managing_customers
Feature: Adding a new customer
    In order to connects orders with client
    As an Administrator
    I want to add a customer to the registry

    Background:
        Given I am logged in as an administrator

    @ui
    Scenario: Adding a new customer
        Given I want to create a new customer
        When I specify its first name as "Luke"
        And I specify its last name as "Skywalker"
        And I specify its email as "l.skywalker@gmail.com"
        And I add it
        Then I should be notified that it has been successfully created
        And the customer "l.skywalker@gmail.com" should appear in the registry
