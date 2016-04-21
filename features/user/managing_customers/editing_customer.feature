@managing_customers
Feature: Editing a customer
    In order to change information about a customer
    As an Administrator
    I want to be able to edit the customer

    Background:
        Given I am logged in as an administrator

    @ui
    Scenario: Editing a customer
        Given the store has customer "Frodo Baggins" with email "f.baggins@example.com"
        And I want to edit the customer "f.baggins@example.com"
        When I specify his first name as "Jon"
        And I specify his last name as "Snow"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And this customer with name "Jon Snow" should appear in the store
