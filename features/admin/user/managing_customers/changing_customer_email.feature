@managing_customers
Feature: Changing an email of an existing customer
    In order to be able to help a customer when he wants to change his email
    As an Administrator
    I want to be able to edit the customer's email

    Background:
        Given the store operates on a single channel in "United States"
        And there is a customer "Frodo Baggins" with an email "f.baggins@example.com" and a password "ring"
        And I am logged in as an administrator

    @ui
    Scenario: Changing an email of an existing customer
        When I want to edit this customer
        And I change their email to "strawberry@example.com"
        And I save my changes
        Then I should be notified that it has been successfully edited
        And the customer should be able to log in as "strawberry@example.com" with "ring" password
