@managing_customers
Feature: Seeing customer's details
    In order to see customer's details in the store
    As an Administrator
    I want to be able to show specific customer's page

    Background:
        Given the store has customer "f.baggins@example.com" since "2011-01-10 21:00:00"
        And his name is "Frodo Baggins"
        And customer "f.baggins@shire.me" has shipping address "Hobbiton", "Bag End", "1", "Shire" for "Frodo Baggins"
        And customer "f.baggins@shire.me" has billing address "Rivendell", "The Last Homely House", "7", "Eriador" for "Bilbo Baggins"
        And I am logged in as an administrator

    @todo
    Scenario: Seeing customer basic information
        When I view details of the customer "f.baggins@shire.me"
        Then his name should be "Frodo Baggins"
        And his registration date should be "2011-01-10 21:00:00"
        And his email should be "f.baggins@shire.me"

    @todo
    Scenario: Seeing customer addresses
        When I view details of the customer "f.baggins@shire.me"
        Then his shipping address should be "Hobbiton", "Bag End", "1", "Shire" for "Frodo Baggins"
        And his billing address should be "Rivendell", "The Last Homely House", "7", "Eriador" for "Bilbo Baggins"
g
