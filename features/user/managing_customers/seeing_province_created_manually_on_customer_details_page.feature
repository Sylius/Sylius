@managing_customers
Feature: Seeing a province on customer's details
    In order to see a country's province defined by customer on theirs details page
    As an Administrator
    I want to be able to see specific customer's page with provinces in the addresses

    @ui
    Scenario: Seeing customer's addresses
        Given I am logged in as an administrator
        And the store has customer "f.baggins@shire.me" with name "Frodo Baggins" since "2011-01-10 21:00"
        And his shipping address is "Frodo Baggins", "Bag End", "12-1321", "Hobbiton", "United Kingdom", "East of England"
        And his billing address is "Bilbo Baggins", "The Last Homely House", "7", "Rivendell", "United Kingdom", "West of England"
        When I view details of the customer "f.baggins@shire.me"
        Then the province in the shipping address should be "East of England"
        Then the province in the billing address should be "West of England"
