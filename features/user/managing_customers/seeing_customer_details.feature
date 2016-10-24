@managing_customers
Feature: Seeing customer's details
    In order to see customer's details in the store
    As an Administrator
    I want to be able to show specific customer's page

    Background:
        Given I am logged in as an administrator
        And the store has customer "f.baggins@shire.me" with name "Frodo Baggins" since "2011-01-10 21:00"

    @ui
    Scenario: Seeing customer's basic information
        When I view details of the customer "f.baggins@shire.me"
        Then his name should be "Frodo Baggins"
        And he should be registered since "2011-01-10 21:00"
        And his email should be "f.baggins@shire.me"

    @ui
    Scenario: Seeing customer's addresses
        Given his default address is "Hobbiton", "Bag End", "1", "New Zealand" for "Frodo Baggins"
        When I view details of the customer "f.baggins@shire.me"
        Then his default address should be "Frodo Baggins, Bag End, Hobbiton, NEW ZEALAND 1"

    @ui
    Scenario: Seeing information about no existing account for a given customer
        When I view details of the customer "f.baggins@shire.me"
        Then I should see information about no existing account for this customer

    @ui
    Scenario: Seeing information about subscription to the newsletter
        Given the customer subscribed to the newsletter
        When I view details of the customer "f.baggins@shire.me"
        Then I should see that this customer is subscribed to the newsletter
