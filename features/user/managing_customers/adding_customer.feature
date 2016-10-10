@managing_customers
Feature: Adding a new customer
    In order to track information about my customers
    As an Administrator
    I want to add a customer to the store

    Background:
        Given the store has a customer group "Retail"
        And I am logged in as an administrator

    @ui
    Scenario: Adding a new customer
        Given I want to create a new customer
        When I specify their email as "l.skywalker@gmail.com"
        And I add them
        Then I should be notified that it has been successfully created
        And the customer "l.skywalker@gmail.com" should appear in the store
        
    @ui
    Scenario: Adding a new customer with full details
        Given I want to create a new customer
        When I specify their first name as "Luke"
        And I specify their last name as "Skywalker"
        And I specify their email as "l.skywalker@gmail.com"
        And I specify its birthday as "1892-01-03"
        And I select "Male" as its gender
        And I select "Retail" as their group
        And I add them
        Then I should be notified that it has been successfully created
        And the customer "l.skywalker@gmail.com" should appear in the store
