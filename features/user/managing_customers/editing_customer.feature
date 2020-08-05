@managing_customers
Feature: Editing a customer
    In order to change information about a customer
    As an Administrator
    I want to be able to edit the customer

    Background:
        Given I am logged in as an administrator

    @ui
    Scenario: Changing first and last name of an existing customer
        Given the store has customer "Frodo Baggins" with email "f.baggins@example.com"
        And I want to edit this customer
        When I specify his first name as "Jon"
        And I specify his last name as "Snow"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this customer with name "Jon Snow" should appear in the store

    @ui
    Scenario: Removing first and last name from an existing customer
        Given the store has customer "Luke Skywalker" with email "l.skywalker@gmail.com"
        And I want to edit this customer
        When I remove its first name
        And I remove its last name
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this customer should have an empty first name
        And this customer should have an empty last name

    @ui
    Scenario: Making an existing customer subscribed to the newsletter
        Given the store has customer "Mike Ross" with email "ross@teammike.com"
        When I want to edit this customer
        And I make them subscribed to the newsletter
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this customer should be subscribed to the newsletter

    @ui
    Scenario: Selecting a group for an existing customer
        Given the store has a customer group "Retail"
        And the store has customer "Mike Ross" with email "ross@teammike.com"
        When I want to edit this customer
        And I select "Retail" as their group
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this customer should have "Retail" as their group
