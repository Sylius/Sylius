@managing_customers
Feature: Seeing a province on customer's details
    In order to see a country's province defined by customer on theirs details page
    As an Administrator
    I want to be able to see specific customer's page with provinces in the addresses

    @ui
    Scenario: Seeing customer's addresses
        Given the store operates in "United Kingdom"
        And the store has customer "f.baggins@shire.me" with name "Frodo Baggins" since "2011-01-10 21:00"
        And their default address is "Frodo Baggins", "Bag End", "12-1321", "Hobbiton", "United Kingdom", "East of England"
        And I am logged in as an administrator
        When I view details of the customer "f.baggins@shire.me"
        Then the province in the default address should be "East of England"
